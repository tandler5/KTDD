<?php

use Illuminate\Support\Facades\Route;

Route::post('/rentals', [\App\Http\Controllers\RentalController::class, 'store']);
