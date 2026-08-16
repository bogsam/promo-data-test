<?php

declare(strict_types=1);

Route::middleware('web')->group(static function (): void {
    Route::redirect('/', '/report-processes');
});
