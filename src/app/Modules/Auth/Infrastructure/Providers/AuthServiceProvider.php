<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Override;

class AuthServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->loadMigrationsFrom(
            paths: app_path(path: 'Modules/Auth/Infrastructure/Database/Migrations')
        );
    }
}
