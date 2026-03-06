<?php

use App\Http\Controllers\Panelis\DatabaseController;
use Illuminate\Support\Facades\Route;

Route::get('/database/download/{file}', [DatabaseController::class, 'download'])->name('database.download');
