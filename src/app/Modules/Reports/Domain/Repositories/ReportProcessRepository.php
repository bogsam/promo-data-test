<?php

declare(strict_types=1);

namespace App\Modules\Reports\Domain\Repositories;

use App\Modules\Reports\Domain\Entities\ReportProcess;
use App\Modules\Shared\ValueObjects\Id;

interface ReportProcessRepository
{
    public function save(ReportProcess $reportProcess): ReportProcess;

    public function findById(Id $id): ?ReportProcess;

    /**
     * @return list<ReportProcess>
     */
    public function findLatest(): array;
}
