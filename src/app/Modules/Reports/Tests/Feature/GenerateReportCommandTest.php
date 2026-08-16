<?php

declare(strict_types=1);

namespace App\Modules\Reports\Tests\Feature;

use App\Modules\Core\Tests\TestCase;
use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Database\Seeders\ProcessStatusSeeder;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use App\Modules\Reports\Infrastructure\Queues\Jobs\GenerateReportFileJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\Console\Command\Command;

final class GenerateReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_report_process_and_dispatches_file_generation_job(): void
    {
        Bus::fake();
        $this->seed(ProcessStatusSeeder::class);

        $this
            ->artisan('report:generate', ['category_id' => 42])
            ->assertExitCode(Command::SUCCESS);

        $reportProcess = ReportProcess::query()->with(relations: 'status')->sole();

        self::assertSame(42, $reportProcess->rp_category_id);
        self::assertSame(ReportProcessStatus::Started->label(), $reportProcess->status->ps_name);
        Bus::assertDispatched(
            GenerateReportFileJob::class,
            static fn (GenerateReportFileJob $job): bool => $job->processId === $reportProcess->rp_id,
        );
    }

    public function test_it_fails_when_category_id_is_not_positive_integer(): void
    {
        Bus::fake();

        $this
            ->artisan('report:generate', ['category_id' => 'milk'])
            ->expectsOutput('The category_id must be a positive integer.')
            ->assertExitCode(exitCode: Command::FAILURE);

        self::assertDatabaseCount('report_process', 0);
        Bus::assertNotDispatched(GenerateReportFileJob::class);
    }
}
