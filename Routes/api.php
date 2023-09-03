<?php

use Illuminate\Support\Facades\Route;
use Modules\Telegram\Http\Controllers\TelegramController;

Route::middleware('auth.api')->prefix('telegram')->group(function () {
    Route::get('/', [TelegramController::class, 'getMessages']);
    Route::match(['get', 'post'],'/send', [TelegramController::class, 'sendMessage']);
});