<?php

declare(strict_types=1);

use App\Modules\Auth\Infrastructure\Providers\AuthServiceProvider;
use App\Modules\Catalog\Infrastructure\Providers\CatalogServiceProvider;
use App\Modules\Core\Infrastructure\Providers\CoreServiceProvider;
use App\Modules\Reports\Infrastructure\Providers\ReportServiceProvider;

return [
    AuthServiceProvider::class,
    CoreServiceProvider::class,
    CatalogServiceProvider::class,
    ReportServiceProvider::class,
];
