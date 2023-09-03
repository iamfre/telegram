<?php

use Illuminate\Support\Facades\Route;

Route::prefix('telegram')->middleware('auth:admin')->group(function() {
    // TODO: routes
});
