<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Data;

use App\Modules\Reports\Domain\Entities\ReportProcess;
use DateTimeImmutable;

final readonly class ReportProcessListItemData
{
    public function __construct(
        public int $processId,
        public int $pid,
        public int $categoryId,
        public string $status,
        public DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $finishedAt,
        public ?int $executionTime,
        public ?string $filePath,
        public ?string $fileName,
    ) {}

    public static function fromReportProcess(ReportProcess $reportProcess): self
    {
        return new self(
            processId: $reportProcess->id()->value(),
            pid: $reportProcess->pid(),
            categoryId: $reportProcess->categoryId()->value(),
            status: $reportProcess->status()->label(),
            startedAt: $reportProcess->startedAt(),
            finishedAt: $reportProcess->finishedAt(),
            executionTime: $reportProcess->executionTimeInSeconds(),
            filePath: $reportProcess->filePath(),
            fileName: $reportProcess->filePath() !== null ? basename(path: $reportProcess->filePath()) : null,
        );
    }
}
