<?php

use App\Http\Controllers\Webhook\MootaController;
use Illuminate\Support\Facades\Route;

Route::post('/moota', MootaController::class);
