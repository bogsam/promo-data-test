<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Exceptions;

use RuntimeException;

final class ReportProcessNotFoundException extends RuntimeException
{
    public function __construct(int $processId)
    {
        parent::__construct(message: sprintf('Report process %d was not found.', $processId));
    }
}
