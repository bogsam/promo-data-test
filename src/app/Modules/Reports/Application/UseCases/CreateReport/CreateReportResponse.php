<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\CreateReport;

use App\Modules\Reports\Domain\Entities\ReportProcess;
use DateTimeImmutable;

final readonly class CreateReportResponse
{
    public function __construct(
        public int               $processId,
        public int               $categoryId,
        public string            $status,
        public DateTimeImmutable $startedAt,
        public DateTimeImmutable $periodFrom,
        public DateTimeImmutable $periodTo,
    ) {}

    public static function fromReportProcess(ReportProcess $reportProcess): self
    {
        return new self(
            processId:  $reportProcess->id()->value(),
            categoryId: $reportProcess->categoryId()->value(),
            status:     $reportProcess->status()->value,
            startedAt:  $reportProcess->startedAt(),
            periodFrom: $reportProcess->period()->from(),
            periodTo:   $reportProcess->period()->to(),
        );
    }
}
