<?php

declare(strict_types=1);

use App\Modules\Reports\Infrastructure\Console\Commands\GenerateReportCommand;
use Illuminate\Console\Application as ArtisanApplication;

ArtisanApplication::starting(callback: function ($artisan): void {
    $artisan->resolveCommands(commands: [
        GenerateReportCommand::class,
    ]);
});
