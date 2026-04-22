<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;

Route::post('/rentals', [RentalController::class, 'store']);
Route::post('/rentals/return', [RentalController::class, 'returnBook']);
