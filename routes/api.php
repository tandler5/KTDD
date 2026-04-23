<?php

use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthCheckController::class);
Route::post('/rentals', [RentalController::class, 'store']);
Route::post('/rentals/return', [RentalController::class, 'returnBook']);
