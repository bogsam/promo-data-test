<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Database\Factories;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportProcess>
 */
class ReportFactory extends Factory
{
    protected $model = ReportProcess::class;

    public function definition(): array
    {
        $startedStatusId = ProcessStatus::query()
            ->where('ps_name', ReportProcessStatus::Started->label())
            ->value(column: 'ps_id') ?? 1;

        return [
            'rp_pid'                    => $this->faker->numberBetween(int1: 1, int2: 1000),
            'rp_category_id'            => $this->faker->numberBetween(int1: 1, int2: 1000),
            'rp_period_from'            => now()->subDays(7),
            'rp_period_to'              => now(),
            'ps_id'                     => $startedStatusId,
            'rp_start_datetime'         => now(),
            'rp_finish_datetime'        => null,
            'rp_exec_time'              => null,
            'rp_file_save_path'         => null,
            'rp_error_message'          => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(state: fn (): array => [
            'ps_id' => ProcessStatus::query()
                ->where('ps_name', ReportProcessStatus::Completed->label())
                ->value(column: 'ps_id') ?? 1,
            'rp_finish_datetime'        => CarbonImmutable::now(),
            'rp_exec_time'              => 300000,
            'rp_file_save_path'         => 'reports/report.csv',
            'rp_error_message'          => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(state: fn (): array => [
            'ps_id' => ProcessStatus::query()
                ->where('ps_name', ReportProcessStatus::Failed->label())
                ->value(column: 'ps_id') ?? 1,
            'rp_finish_datetime'        => CarbonImmutable::now(),
            'rp_exec_time'              => 300000,
            'rp_file_save_path'         => null,
            'rp_error_message'          => 'Unable to write file.',
        ]);
    }

    public function processing(): static
    {
        return $this->state(state: fn (): array => [
            'ps_id' => ProcessStatus::query()
                ->where('ps_name', ReportProcessStatus::Processing->label())
                ->value(column: 'ps_id') ?? 1,
        ]);
    }
}
