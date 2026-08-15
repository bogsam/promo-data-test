<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Http\Controllers;

use App\Modules\Core\Infrastructure\Http\Controllers\Controller;
use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportProcessDownloadController extends Controller
{
    public function __invoke(ReportProcess $reportProcess): StreamedResponse
    {
        if ($reportProcess->status->code !== ReportProcessStatus::Completed->code()) {
            abort(code: Response::HTTP_CONFLICT, message: 'Report file is available only for completed processes.');
        }

        $filePath = $reportProcess->file_path;

        if ($filePath === null || ! Storage::disk('local')->exists($filePath)) {
            abort(code: Response::HTTP_NOT_FOUND, message: 'Report file not found.');
        }

        return Storage::disk('local')->download(
            $filePath,
            basename(path: $filePath),
            ['Content-Type' => 'text/csv'],
        );
    }
}
