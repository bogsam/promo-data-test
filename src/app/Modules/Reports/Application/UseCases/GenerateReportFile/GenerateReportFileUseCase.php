<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\GenerateReportFile;

use App\Modules\Reports\Application\Contracts\ReportFileWriter;
use App\Modules\Reports\Application\Data\GenerateReportFileData;
use App\Modules\Reports\Application\Exceptions\ReportDataNotFoundException;
use App\Modules\Reports\Application\Exceptions\ReportProcessNotFoundException;
use App\Modules\Reports\Domain\Repositories\ReportProcessRepository;
use App\Modules\Shared\Application\Contracts\CatalogReader;
use App\Modules\Shared\Domain\ValueObjects\Id;
use DateMalformedStringException;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class GenerateReportFileUseCase
{
    public function __construct(
        private ReportProcessRepository  $reportProcessRepository,
        private CatalogReader            $catalogReader,
        private ReportFileWriter         $reportFileWriter,
        private LoggerInterface          $logger,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws Throwable
     */
    public function execute(GenerateReportFileRequest $request): GenerateReportFileResponse
    {
        $reportProcess = $this->reportProcessRepository->findById(new Id($request->processId));

        if ($reportProcess === null) {
            throw new ReportProcessNotFoundException($request->processId);
        }

        $reportProcess->markProcessing();
        $this->reportProcessRepository->save($reportProcess);

        try {
            $productData = $this->catalogReader->findProductDataByCategoryAndPeriod(
                $reportProcess->categoryId(),
                $reportProcess->period(),
            );

            if ($productData === []) {
                throw new ReportDataNotFoundException(categoryId: $reportProcess->categoryId()->value());
            }

            // Имя файла требует один manufacturer_id; строки отчёта уже отфильтрованы
            // по категории, поэтому берём стабильный идентификатор из первой строки.
            $manufacturerId = $productData[0]->manufacturerId;

            $reportFileData = new GenerateReportFileData(
                processId:          $reportProcess->id()?->value() ?? $request->processId,
                categoryId:         $reportProcess->categoryId()->value(),
                manufacturerId:     $manufacturerId,
                startedAt:          $reportProcess->startedAt(),
                productPriceData:   $productData,
            );

            $filePath   = $this->reportFileWriter->write($reportFileData);
            $finishedAt = new DateTimeImmutable;

            $reportProcess->markCompleted(filePath: $filePath, finishedAt: $finishedAt);
            $this->reportProcessRepository->save($reportProcess);

            return GenerateReportFileResponse::fromReportProcess(
                reportProcess:  $reportProcess,
                manufacturerId: $manufacturerId,
                filePath:       $filePath,
                rowsCount:      count(value: $productData),
            );

        } catch (Throwable $throwable) {
            $this->logger->error('Report generation failed.', [
                'process_id' => $request->processId,
                'exception'  => $throwable::class,
                'message'    => $throwable->getMessage(),
            ]);

            $reportProcess->markFailed(errorMessage: $throwable->getMessage(), finishedAt: new DateTimeImmutable);
            $this->reportProcessRepository->save($reportProcess);

            throw $throwable;
        }
    }
}
