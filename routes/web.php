<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\FullCalendarController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\CacheJsonResponse;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/phpinfo', function () {return phpinfo();});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', 'offices');

    // Inertia страницы с полной иерархией для навигации
    Route::prefix('offices/{office}')->group(function () {
        Route::apiResource('rooms', RoomController::class);
        Route::prefix('rooms/{room}')->group(function () {
            Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('event.edit');
        });
    });

    // API операции с упрощенными маршрутами
    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/{event}/data', [EventController::class, 'getEventData'])->name('events.data');
    });

    // Редиректы для упрощенного доступа к событиям
    Route::get('/events/{event}/edit', [EventController::class, 'redirect_edit'])->name('event.redirect.edit');

    Route::resource('offices', OfficeController::class);

    Route::any('calendar', [FullCalendarController::class, 'index'])
        ->middleware(CacheJsonResponse::class . ':1')
        ->name('FullCalendarRoute');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
