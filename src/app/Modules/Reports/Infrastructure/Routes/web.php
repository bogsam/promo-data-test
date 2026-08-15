<?php

declare(strict_types=1);

use App\Modules\Reports\Infrastructure\Http\Controllers\ReportProcessDownloadController;
use App\Modules\Reports\Infrastructure\Http\Controllers\ReportProcessIndexController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(static function (): void {
    Route::get('/report-processes', ReportProcessIndexController::class)
        ->name(name: 'report-processes.index');

    Route::get('/report-processes/{reportProcess}/download', ReportProcessDownloadController::class)
        ->name(name: 'report-processes.download');
});
