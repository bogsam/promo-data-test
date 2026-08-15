<?php

declare(strict_types=1);

namespace App\Modules\Core\Infrastructure\Providers;

use App\Modules\Core\Infrastructure\Database\Seeders\DatabaseSeeder;
use Illuminate\Support\ServiceProvider;
use Override;

class CoreServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->loadMigrationsFrom(
            paths: app_path(path: 'Modules/Core/Infrastructure/Database/Migrations')
        );

        if (! class_exists('Database\\Seeders\\DatabaseSeeder', false)) {
            class_alias(DatabaseSeeder::class, 'Database\\Seeders\\DatabaseSeeder');
        }
    }
}
