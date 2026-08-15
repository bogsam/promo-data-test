<?php

Route::middleware('web')->group(static function (): void {
    Route::redirect('/', '/report-processes');
});
