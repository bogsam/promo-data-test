<?php

declare(strict_types=1);

namespace App\Modules\Reports\Tests\Feature;

use App\Modules\Core\Tests\TestCase;
use App\Modules\Reports\Infrastructure\Database\Seeders\ProcessStatusSeeder;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

final class ReportProcessDownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_downloads_completed_report_file(): void
    {
        $this->seed(ProcessStatusSeeder::class);

        Storage::fake(disk: 'local');
        Storage::disk('local')->put('reports/report.csv', "foo,bar\n1,2\n");

        $reportProcess = ReportProcess::factory()
            ->completed()
            ->create(attributes: [
                'rp_file_save_path' => 'reports/report.csv',
            ]);

        $response = $this->get("/report-processes/{$reportProcess->rp_id}/download");

        $response->assertOk();
        $response->assertDownload(filename: 'report.csv');
        $response->assertHeader(headerName: 'content-type', value: 'text/csv; charset=utf-8');
    }

    public function test_it_returns_not_found_for_missing_report_process(): void
    {
        $response = $this->get('/report-processes/999999/download');

        $response->assertNotFound();
    }

    public function test_it_returns_conflict_for_non_completed_report_process(): void
    {
        $this->seed(ProcessStatusSeeder::class);

        $reportProcess = ReportProcess::factory()
            ->processing()
            ->create();

        $response = $this->get("/report-processes/{$reportProcess->rp_id}/download");

        $response->assertConflict();
    }
}
