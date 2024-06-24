<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SitemapController;
use AshAllenDesign\ShortURL\Facades\ShortURL;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [ScheduleController::class, 'index'])->name('index');

Route::get('/contact', [MessageController::class, 'form'])->name('message.form');
Route::post('/contact', [MessageController::class, 'submit'])->name('message.submit');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/organizer/{organizer:slug}', [OrganizerController::class, 'view'])->name('organizer.view');

Route::get('/archive', [ScheduleController::class, 'archive'])->name('schedule.archive');
Route::get('/event/{slug}', [ScheduleController::class, 'view'])->name('schedule.view');
Route::get('/{year}/{month?}', [ScheduleController::class, 'filter'])
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+'])
    ->name('schedule.filter');

ShortURL::routes();
