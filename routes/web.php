<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\FullCalendarController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\CacheJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/welcome_laravel', function () {
    return Inertia::render('Welcome_default', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/phpinfo', function () {return phpinfo();});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::redirect('/', 'offices');

    Route::resource('offices', OfficeController::class);
    Route::prefix('offices/{office}')->group(function () {
        Route::apiResource('rooms', RoomController::class)->middleware(CacheJsonResponse::class);
        Route::prefix('rooms/{room}')->group(function () {
            Route::get('/events/{event}', [EventController::class, 'show'])->name('event.show');

        });
    });

    Route::any('calendar', [FullCalendarController::class, 'index'])->middleware(CacheJsonResponse::class . ':1')->name('FullCalendarRoute');

    Route::post('events', [EventController::class, 'store'])->name('events.store');


});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
