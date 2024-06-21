<?php

use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SitemapController;
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

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/organizer/{organizer:slug}', [OrganizerController::class, 'view'])->name('organizer.view');

Route::get('/go', [ScheduleController::class, 'go'])->name('schedule.go');
Route::get('/archive', [ScheduleController::class, 'archive'])->name('schedule.archive');
Route::get('/{year}/{month?}', [ScheduleController::class, 'filter'])
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+'])
    ->name('schedule.filter');
Route::get('/{year}/{slug}', [ScheduleController::class, 'viewLegacy'])->name('schedule.viewLegacy');
Route::get('/{slug}', [ScheduleController::class, 'view'])->name('schedule.view');
