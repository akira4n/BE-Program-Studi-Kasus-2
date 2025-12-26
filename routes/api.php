<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TransactionController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::patch('/transactions/{id}', [TransactionController::class, 'update']);

    Route::middleware('role:admin,organizer')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::patch('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);
    });

    Route::middleware('role:admin,user')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
    });
});
