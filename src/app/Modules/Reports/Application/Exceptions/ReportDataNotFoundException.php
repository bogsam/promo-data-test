<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Exceptions;

use RuntimeException;

final class ReportDataNotFoundException extends RuntimeException
{
    public function __construct(int $categoryId)
    {
        parent::__construct(message: sprintf('No reportable data found for category %d.', $categoryId));
    }
}
