<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Book;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Exception;

class RentalController extends Controller
{
    protected RentalService $rentalService;

    public function __construct(RentalService $rentalService)
    {
        $this->rentalService = $rentalService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);

        try {
            $user = isset($validated['user_id'])
                ? User::findOrFail($validated['user_id'])
                : auth()->user();

            if (!$user) {
                throw new Exception('User not authenticated');
            }
            $book = Book::findOrFail($validated['book_id']);

            $rental = $this->rentalService->rentBook($user, $book);

            if ($request->wantsJson()) {
                return response()->json($rental, 201);
            }

            return back()->with('success', 'Book rented successfully');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
        ]);

        try {
            $book = Book::findOrFail($validated['book_id']);
            $this->rentalService->returnBook($book);

            return response()->json(['message' => 'Book returned successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
