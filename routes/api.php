<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

Route::post('/rentals', [RentalController::class, 'store']);
Route::post('/rentals/return', [RentalController::class, 'returnBook']);
