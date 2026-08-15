<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Providers;

use App\Modules\Catalog\Domain\Repositories\CatalogRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Repositories\EloquentCatalogRepository;
use App\Modules\Shared\Application\Contracts\CatalogReader;
use Illuminate\Support\ServiceProvider;
use Override;

class CatalogServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(CatalogRepository::class, EloquentCatalogRepository::class);
        $this->app->bind(CatalogReader::class,     EloquentCatalogRepository::class);

        $this->loadMigrationsFrom(
            paths: app_path(path: 'Modules/Catalog/Infrastructure/Database/Migrations')
        );
    }
}
