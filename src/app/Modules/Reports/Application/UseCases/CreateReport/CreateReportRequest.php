<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\CreateReport;

use DateTimeImmutable;

final readonly class CreateReportRequest
{
    public function __construct(
        public int               $pid,
        public int               $categoryId,
        public DateTimeImmutable $startedAt,
    ) {}
}
