<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\CreateReport;

use App\Modules\Reports\Domain\Entities\ReportProcess;
use App\Modules\Reports\Domain\Repositories\ReportProcessRepository;
use App\Modules\Shared\Domain\ValueObjects\Id;
use App\Modules\Shared\Domain\ValueObjects\Period;
use DateInterval;
use DateInvalidOperationException;

final readonly class CreateReportUseCase
{
    public function __construct(
        private ReportProcessRepository $reportProcessRepository,
    ) {}

    /**
     * @throws DateInvalidOperationException
     */
    public function execute(CreateReportRequest $request): CreateReportResponse
    {
        $period = Period::between(
            from: $request->startedAt->sub(interval: new DateInterval(duration: 'P7D')),
            to: $request->startedAt,
        );

        $reportProcess = ReportProcess::create(
            pid: $request->pid,
            categoryId: new Id($request->categoryId),
            period: $period,
            startedAt: $request->startedAt,
        );

        $savedReportProcess = $this->reportProcessRepository->save($reportProcess);

        return CreateReportResponse::fromReportProcess(reportProcess: $savedReportProcess);
    }
}
