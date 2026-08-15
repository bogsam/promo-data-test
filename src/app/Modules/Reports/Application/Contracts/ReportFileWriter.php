<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Contracts;

use App\Modules\Reports\Application\Data\GenerateReportFileData;

interface ReportFileWriter
{
    public function write(GenerateReportFileData $data): string;
}
