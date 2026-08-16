<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Database\Seeders;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ReportProcess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReportProcessSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCompletedReport(
            categoryId: 10,
            manufacturerId: 1,
            startedAt: CarbonImmutable::now()->subDay()->setTime(hour: 9, minute: 30),
            fileRows: [
                'Northwind Trading,Alpha Coffee Beans,10.75,2026-07-30 10:00:00',
                'Northwind Trading,Alpha Coffee Beans,13.20,2026-07-30 18:00:00',
            ],
        );

        $this->seedCompletedReport(
            categoryId: 20,
            manufacturerId: 2,
            startedAt: CarbonImmutable::now()->subHours(5)->setTime(hour: 14, minute: 15),
            fileRows: [
                'Acme Goods,Delta Biscuit,4.95,2026-07-31 09:00:00',
                'Acme Goods,Delta Biscuit,5.55,2026-07-31 17:00:00',
            ],
        );

        ReportProcess::factory()->failed()->create(attributes: [
            'rp_category_id'           => 20,
            'rp_start_datetime'        => CarbonImmutable::now()->subHours(2),
            'rp_finish_datetime'       => CarbonImmutable::now()->subHours(2)->addMinutes(1),
            'rp_exec_time'             => 60_000,
            'rp_error_message'         => 'Unable to write file.',
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
        $fileName = sprintf('report_%d_%d_%s.csv', $manufacturerId, $categoryId, $startedAt->format(format: 'Y-m-d_H-i-s'));
        $filePath = 'reports/' . $fileName;
        $csv = implode(separator: PHP_EOL, array: [
            'manufacturer_name,product_name,price,price_date',
            ...$fileRows,
        ]) . PHP_EOL;

        if (! Storage::disk('local')->put($filePath, $csv)) {
            throw new RuntimeException(message: sprintf('Unable to seed report file: %s', $filePath));
        }

        $completedStatusId = ProcessStatus::query()
            ->where('ps_name', ReportProcessStatus::Completed->label())
            ->value(column: 'ps_id') ?? 1;

        ReportProcess::factory()->completed()->create(attributes: [
            'rp_category_id'            => $categoryId,
            'rp_start_datetime'         => $startedAt,
            'rp_finish_datetime'        => $startedAt->addSeconds(42),
            'rp_exec_time'              => 42_000,
            'ps_id'                     => $completedStatusId,
            'rp_file_save_path'         => $filePath,
        ]);
    }
}
