<?php

use App\Http\Controllers\FullCalendarController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
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
    Route::prefix('offices/{office_id}')->group(function () {
        Route::apiResource('rooms', RoomController::class);
    });

    Route::any('calendar', [FullCalendarController::class, 'index'])->name('FullCalendarRoute');


});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
