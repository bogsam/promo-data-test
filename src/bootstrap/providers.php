<?php

declare(strict_types=1);

use App\Modules\Auth\Infrastructure\Providers\AuthServiceProvider;
use App\Modules\Core\Infrastructure\Providers\CoreServiceProvider;

return [
    AuthServiceProvider::class,
    CoreServiceProvider::class,
];
