<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(path: __DIR__))
    ->withRouting(
        health: '/up',
    )
    ->withExceptions(using: function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            callback: fn (Request $request) => $request->is(patterns1: 'api/*'),
        );
    })->create();
