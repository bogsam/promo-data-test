<?php

declare(strict_types=1);

namespace App\Modules\Reports\Application\Queries;

use App\Modules\Reports\Application\Data\ReportProcessListItemData;
use App\Modules\Reports\Domain\Repositories\ReportProcessRepository;

final readonly class ReportProcessListQuery
{
    public function __construct(
        private ReportProcessRepository $reportProcessRepository,
    ) {}

    /**
     * @return list<ReportProcessListItemData>
     */
    public function execute(): array
    {
        return array_map(
            callback: ReportProcessListItemData::fromReportProcess(...),
            array:    $this->reportProcessRepository->findLatest(),
        );
    }
}
