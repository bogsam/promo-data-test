<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Database\Factories;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ReportProcess>
 */
class ReportFactory extends Factory
{
    protected $model = ReportProcess::class;

    public function definition(): array
    {
        $startedStatusId = ProcessStatus::query()
            ->where('code', ReportProcessStatus::Started->code())
            ->value(column: 'id') ?? 1;

        return [
            'pid'               => $this->faker->numberBetween(int1: 1, int2: 1000),
            'category_id'       => $this->faker->numberBetween(int1: 1, int2: 1000),
            'period_from'       => now()->subDays(7),
            'period_to'         => now(),
            'status_id'         => $startedStatusId,
            'started_at'        => now(),
            'finished_at'       => null,
            'execution_time_ms' => null,
            'file_name'         => null,
            'file_path'         => null,
            'error_message'     => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(state: fn (): array => [
            'status_id' => ProcessStatus::query()
                ->where('code', ReportProcessStatus::Completed->code())
                ->value(column: 'id') ?? 1,
            'finished_at'       => CarbonImmutable::now(),
            'execution_time_ms' => 300000,
            'file_name'         => 'report.csv',
            'file_path'         => 'reports/report.csv',
            'error_message'     => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(state: fn (): array => [
            'status_id' => ProcessStatus::query()
                ->where('code', ReportProcessStatus::Failed->code())
                ->value(column: 'id') ?? 1,
            'finished_at'       => CarbonImmutable::now(),
            'execution_time_ms' => 300000,
            'file_name'         => null,
            'file_path'         => null,
            'error_message'     => 'Unable to write file.',
        ]);
    }

    public function processing(): static
    {
        return $this->state(state: fn (): array => [
            'status_id' => ProcessStatus::query()
                ->where('code', ReportProcessStatus::Processing->code())
                ->value(column: 'id') ?? 1,
        ]);
    }
}
