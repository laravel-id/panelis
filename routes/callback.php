<?php

use App\Http\Controllers\OAuth\DropboxController;
use Illuminate\Support\Facades\Route;

Route::get('/dropbox', DropboxController::class)->name('dropbox');
