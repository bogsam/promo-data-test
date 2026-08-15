<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\UseCases\GenerateReportFile;

final readonly class GenerateReportFileRequest
{
    public function __construct(
        public int $processId,
    ) {}
}
