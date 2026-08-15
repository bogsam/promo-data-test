<?php

declare(strict_types=1);

namespace App\Modules\Reports\Infrastructure\Providers;

use App\Modules\Reports\Application\Contracts\ReportFileWriter;
use App\Modules\Reports\Domain\Repositories\ReportProcessRepository;
use App\Modules\Reports\Infrastructure\Persistence\Repositories\EloquentReportProcessRepository;
use App\Modules\Reports\Infrastructure\Services\LaravelReportFileWriter;
use Illuminate\Support\ServiceProvider;
use Override;

class ReportServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(ReportProcessRepository::class, EloquentReportProcessRepository::class);
        $this->app->bind(ReportFileWriter::class, LaravelReportFileWriter::class);

        $this->loadMigrationsFrom(
            paths: app_path(path: 'Modules/Reports/Infrastructure/Database/Migrations')
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(path: app_path(path: 'Modules/Reports/Infrastructure/Routes/web.php'));
        $this->loadRoutesFrom(path: app_path(path: 'Modules/Reports/Infrastructure/Routes/console.php'));
    }
}
