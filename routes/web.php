<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::prefix('api')->group(function () {
    Route::get('/auth/google/redirect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback']);
});

require __DIR__.'/auth.php';
