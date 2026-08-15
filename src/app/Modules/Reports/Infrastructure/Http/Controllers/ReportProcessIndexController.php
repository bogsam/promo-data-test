<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Http\Controllers;

use App\Modules\Core\Infrastructure\Http\Controllers\Controller;
use App\Modules\Reports\Application\Queries\ReportProcessListQuery;
use Illuminate\Contracts\View\View;

final class ReportProcessIndexController extends Controller
{
    public function __construct(
        private readonly ReportProcessListQuery $reportProcessListQuery,
    ) {}

    public function __invoke(): View
    {
        return view(view: 'reports.processes.index', data: [
            'processes' => $this->reportProcessListQuery->execute(),
        ]);
    }
}
