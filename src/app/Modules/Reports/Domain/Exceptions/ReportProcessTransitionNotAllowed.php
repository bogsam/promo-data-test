<?php

declare(strict_types=1);

namespace App\Modules\Reports\Domain\Exceptions;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Shared\Domain\Exceptions\DomainException;

final class ReportProcessTransitionNotAllowed extends DomainException
{
    public function __construct(
        ?int                $processId,
        ReportProcessStatus $currentStatus,
        ReportProcessStatus $targetStatus,
    ) {
        parent::__construct(message: sprintf(
            'Cannot transition report process %s from %s to %s.',
            $processId,
            $currentStatus->label(),
            $targetStatus->label(),
        ));
    }
}
