<?php

use Illuminate\Support\Facades\Route;
use Modules\Database\Controllers\AuthController;
use Modules\Database\Controllers\DatabaseController;

Route::get('/redirect', [AuthController::class, 'redirect'])->name('redirect');
Route::get('/callback', [AuthController::class, 'callback'])->name('callback');

Route::get('/download/{file}', [DatabaseController::class, 'download'])->name('download');
