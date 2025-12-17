<?php

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::prefix('players')->group(function () {
   Route::get('get', [PlayerController::class, 'index']);
});

Route::prefix('player')->group(function () {
    Route::post('new', [PlayerController::class, 'store']);
});

Route::prefix('player/{id}')->group(function () {
    Route::post('delete', [PlayerController::class, 'destroy']);
});

Route::prefix('teams')->group(function () {
    Route::get('get', [TeamController::class, 'index']);
});

Route::prefix('team')->group(function () {
    Route::post('new', [TeamController::class, 'store']);
});

Route::prefix('team/{id}')->group(function () {
    Route::get('/', [TeamController::class, 'show']);
    Route::put('update', [TeamController::class, 'update']);
    Route::post('delete', [TeamController::class, 'destroy']);
    Route::post('add-player', [TeamController::class, 'addPlayer']);
    Route::post('remove-player/{playerId}', [TeamController::class, 'removePlayer']);
});
