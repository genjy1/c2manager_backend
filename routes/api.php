<?php

use App\Http\Controllers\PlayerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);


Route::prefix('players')->group(function () {
   Route::get('get', [PlayerController::class, 'index']);
   Route::get('crawl', [PlayerController::class, 'crawl_players']);
});

Route::prefix('player')->group(function () {
    Route::post('new', [PlayerController::class, 'store']);
});

Route::prefix('player/{id}')->group(function () {
    Route::post('delete', [PlayerController::class, 'destroy']);
});
