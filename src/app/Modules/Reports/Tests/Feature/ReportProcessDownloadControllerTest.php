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

        Storage::fake('local');
        Storage::disk('local')->put('reports/report.csv', "foo,bar\n1,2\n");

        $reportProcess = ReportProcess::factory()
            ->completed()
            ->create([
                'file_name' => 'report.csv',
                'file_path' => 'reports/report.csv',
            ]);

        $response = $this->get("/report-processes/{$reportProcess->id}/download");

        $response->assertOk();
        $response->assertDownload('report.csv');
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }
}
