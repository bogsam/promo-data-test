<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Database\Seeders;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ReportProcessSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCompletedReport(
            categoryId:     10,
            manufacturerId: 1,
            startedAt:      CarbonImmutable::now()->subDay()->setTime(9, 30, 0),
            fileRows: [
                'Northwind Trading,Alpha Coffee Beans,10.75,2026-07-30 10:00:00',
                'Northwind Trading,Alpha Coffee Beans,13.20,2026-07-30 18:00:00',
            ],
        );

        $this->seedCompletedReport(
            categoryId:     20,
            manufacturerId: 2,
            startedAt:      CarbonImmutable::now()->subHours(5)->setTime(14, 15, 0),
            fileRows: [
                'Acme Goods,Delta Biscuit,4.95,2026-07-31 09:00:00',
                'Acme Goods,Delta Biscuit,5.55,2026-07-31 17:00:00',
            ],
        );

        ReportProcess::factory()->failed()->create([
            'category_id'       => 20,
            'started_at'        => CarbonImmutable::now()->subHours(2),
            'finished_at'       => CarbonImmutable::now()->subHours(2)->addMinutes(1),
            'execution_time_ms' => 60_000,
            'error_message'     => 'Unable to write file.',
        ]);
    }

    /**
     * @param  list<string>  $fileRows
     */
    private function seedCompletedReport(
        int $categoryId,
        int $manufacturerId,
        CarbonImmutable $startedAt,
        array $fileRows,
    ): void {
        $fileName = sprintf('report_%d_%d_%s.csv', $manufacturerId, $categoryId, $startedAt->format('Y-m-d_H-i-s'));
        $filePath = 'reports/' . $fileName;
        $csv = implode(PHP_EOL, [
            'manufacturer_name,product_name,price,price_date',
            ...$fileRows,
        ]) . PHP_EOL;

        if (! Storage::disk('local')->put($filePath, $csv)) {
            throw new RuntimeException(message: sprintf('Unable to seed report file: %s', $filePath));
        }

        $completedStatusId = ProcessStatus::query()
            ->where('code', ReportProcessStatus::Completed->code())
            ->value(column: 'id') ?? 1;

        ReportProcess::factory()->completed()->create([
            'pid'               => (string) Str::ulid(),
            'category_id'       => $categoryId,
            'started_at'        => $startedAt,
            'finished_at'       => $startedAt->addSeconds(42),
            'execution_time_ms' => 42_000,
            'status_id'         => $completedStatusId,
            'file_name'         => $fileName,
            'file_path'         => $filePath,
        ]);
    }
}
