<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\GenerateReportFile;

use App\Modules\Reports\Domain\Entities\ReportProcess;
use App\Modules\Shared\Domain\ValueObjects\Id;
use DateTimeImmutable;
use RuntimeException;

final readonly class GenerateReportFileResponse
{
    public function __construct(
        public int $processId,
        public int $categoryId,
        public int $manufacturerId,
        public DateTimeImmutable $startedAt,
        public DateTimeImmutable $periodFrom,
        public DateTimeImmutable $periodTo,
        public DateTimeImmutable $finishedAt,
        public string $filePath,
        public string $fileName,
        public int $rowsCount,
    ) {}

    public static function fromReportProcess(
        ReportProcess $reportProcess,
        int $manufacturerId,
        string $filePath,
        int $rowsCount,
    ): self {
        $id = $reportProcess->id();

        if (! $id instanceof Id) {
            throw new RuntimeException(message: 'Report process id is missing.');
        }

        return new self(
            processId: $id->value(),
            categoryId: $reportProcess->categoryId()->value(),
            manufacturerId: $manufacturerId,
            startedAt: $reportProcess->startedAt(),
            periodFrom: $reportProcess->period()->from(),
            periodTo: $reportProcess->period()->to(),
            finishedAt: $reportProcess->finishedAt() ?? throw new RuntimeException(message: 'Report process finishedAt is missing.'),
            filePath: $filePath,
            fileName: basename(path: $filePath),
            rowsCount: $rowsCount,
        );
    }

    public function rowsCount(): int
    {
        return $this->rowsCount;
    }
}
