<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (config('app.demo')) {
        return redirect()->route('filament.admin.auth.login');
    }

    return view('welcome');
});
