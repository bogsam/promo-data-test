<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Database\Seeders;

use App\Modules\Reports\Domain\Enums\ReportProcessStatus;
use App\Modules\Reports\Infrastructure\Persistence\Models\ProcessStatus;
use Illuminate\Database\Seeder;

class ProcessStatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = array_map(
            callback: static fn (ReportProcessStatus $status): array => [
                'code' => $status->code(),
                'name' => $status->label(),
            ],
            array: ReportProcessStatus::cases(),
        );

        ProcessStatus::query()->upsert($rows, ['code'], ['name']);
    }
}
