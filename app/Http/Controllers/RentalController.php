<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
            'user_id' => 'required|integer|exists:users,id',
            'rented_at' => 'required|date',
            'due_date' => 'required|date|after_or_equal:rented_at',
        ]);

        $rental = Rental::create($validated);

        return response()->json($rental, 201);
    }
}
