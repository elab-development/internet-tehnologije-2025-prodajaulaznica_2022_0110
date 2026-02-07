<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Public rute (vraćaju JSON)
Route::get('/events', [EventController::class, 'apiIndex']);
Route::get('/events/{event}', [EventController::class, 'apiShow']);
Route::get('/categories', [CategoryController::class, 'apiIndex']);

// Protected rute (vraćaju JSON)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'apiStore']);
    Route::put('/events/{event}', [EventController::class, 'apiUpdate']);
    Route::delete('/events/{event}', [EventController::class, 'apiDestroy']);
});
