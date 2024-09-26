<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'softDelete']);
    Route::get('/users-deleted', [UserController::class, 'deleted']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::get('/histories/{id}', [HistoryController::class, 'show']);
    Route::delete('/histories/{id}', [HistoryController::class, 'softDelete']);
    Route::post('/histories/{id}/restore', [HistoryController::class, 'restore']);
    Route::delete('/histories/{id}/force-delete', [HistoryController::class, 'forceDelete']);
    Route::post('/histories/{id}/restore-model', [HistoryController::class, 'restoreModel']);
});
