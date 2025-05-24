<?php

use App\Http\Controllers\OfficeController;
use App\Http\Middleware\CacheJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('hi', function () {
        return 'hi';
    });

    Route::get('/offices/{office}/businessHours', [OfficeController::class, 'businessHours'])->middleware(CacheJsonResponse::class);

});
