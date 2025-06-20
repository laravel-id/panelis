<?php

use App\Http\Controllers\Demo\LoginController;

Route::get('/login', [LoginController::class, 'guest'])->name('login');
