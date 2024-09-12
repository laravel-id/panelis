<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Event\ParticipantController;
use App\Http\Controllers\Event\ScheduleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OAuth\DropboxController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriberController;
use App\Http\Middleware\CacheResponse;
use App\Livewire\Participants\Register;
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

Route::get('/subscribe', [SubscriberController::class, 'form'])->name('subscriber.form');
Route::post('/subscribe', [SubscriberController::class, 'submit'])->name('subscriber.submit');
Route::get('/subscribe/{key}', [SubscriberController::class, 'subscribe'])->name('subscriber.subscribe');
Route::get('/unsubscribe/{key}', [SubscriberController::class, 'unsubscribe'])->name('subscriber.unsubscribe');

Route::get('/contact', [MessageController::class, 'form'])->name('message.form');
Route::post('/contact', [MessageController::class, 'submit'])->name('message.submit');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/organizer/{organizer:slug}', [OrganizerController::class, 'view'])->name('organizer.view');

Route::get('/event/{slug}/register', Register::class)->name('participant.register');
Route::get('/participant/{participant:ulid}', [ParticipantController::class, 'view'])->name('participant.view');
Route::get('/participant/status/{participant:ulid}', [ParticipantController::class, 'status'])->name('participant.status');

Route::get('/archive', [ScheduleController::class, 'archive'])->name('schedule.archive');
Route::get('/event/{slug}', [ScheduleController::class, 'view'])
    ->middleware([CacheResponse::class])
    ->name('schedule.view');
Route::get('/{year}/{month?}/{day?}', [ScheduleController::class, 'filter'])
    ->where([
        'year' => '[0-9]+',
        'month' => '[0-9]+',
        'day' => '[0-9]+',
    ])
    ->name('schedule.filter');

Route::get('/dropbox', DropboxController::class)->name('callback.dropbox');

ShortURL::routes();
