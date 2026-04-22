<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Book;
use App\Models\User;
use App\Services\RentalService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Exception;

class RentalController extends Controller
{
    protected RentalService $rentalService;

    public function __construct(RentalService $rentalService)
    {
        $this->rentalService = $rentalService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $rentals = Rental::with(['book', 'user'])
            ->when($request->input('search_book'), function ($query, $search) {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($request->input('search_user'), function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($rental) => [
                'id' => $rental->id,
                'book_title' => $rental->book->title,
                'user_name' => $rental->user->name,
                'rented_at' => $rental->rented_at,
                'due_date' => $rental->due_date,
                'returned_at' => $rental->returned_at,
                'is_overdue' => $rental->returned_at === null && \Carbon\Carbon::parse($rental->due_date)->isPast(),
            ]);

        return Inertia::render('Rentals/Index', [
            'rentals' => $rentals,
            'filters' => $request->only(['search_book', 'search_user', 'per_page']),
        ]);
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

            return back()->with('error', $e->getMessage());
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

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Book returned successfully']);
            }

            return back()->with('success', 'Book returned successfully');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
