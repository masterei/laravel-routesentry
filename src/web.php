<?php

use Illuminate\Support\Facades\Route;
use Masterei\Sentry\General\Config;

Route::prefix(Config::get('route_prefix'))->group(function (){
    Route::get('/', [\Masterei\Sentry\SentryController::class, 'index']);
    Route::get('user', [\Masterei\Sentry\SentryController::class, 'users']);

    Route::prefix('users')->group(function(){
        Route::get('/', []);
    });
});
