<?php

declare(strict_types=1);

namespace App\Modules\Core\Infrastructure\Database\Seeders;

use App\Modules\Catalog\Infrastructure\Database\Seeders\CatalogSeeder;
use App\Modules\Reports\Infrastructure\Database\Seeders\ProcessStatusSeeder;
use App\Modules\Reports\Infrastructure\Database\Seeders\ReportProcessSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(class: [
            CatalogSeeder::class,
            ProcessStatusSeeder::class,
            ReportProcessSeeder::class,
        ]);
    }
}
