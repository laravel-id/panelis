<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriberController;
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

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'form'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    Route::get('/register', [RegisterController::class, 'form'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/schedules.json', [ScheduleController::class, 'json']);

Route::get('/subscribe', [SubscriberController::class, 'form'])->name('subscriber.form');
Route::post('/subscribe', [SubscriberController::class, 'submit'])->name('subscriber.submit');
Route::get('/subscribe/{key}', [SubscriberController::class, 'subscribe'])->name('subscriber.subscribe');
Route::get('/unsubscribe/{key}', [SubscriberController::class, 'unsubscribe'])->name('subscriber.unsubscribe');

Route::get('/contact', [MessageController::class, 'form'])->name('message.form');
Route::post('/contact', [MessageController::class, 'submit'])->name('message.submit');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/organizer/{organizer:slug}', [OrganizerController::class, 'view'])->name('organizer.view');

Route::get('/calendar', [ScheduleController::class, 'calendar'])->name('schedule.calendar');
Route::get('/archive', [ScheduleController::class, 'archive'])->name('schedule.archive');
Route::get('/event/{slug}', [ScheduleController::class, 'view'])->name('schedule.view');
Route::get('/{year}/{month?}', [ScheduleController::class, 'filter'])
    ->where(['year' => '[0-9]+', 'month' => '[0-9]+'])
    ->name('schedule.filter');

ShortURL::routes();
