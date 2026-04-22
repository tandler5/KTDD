<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $books = Book::query()
            ->when($request->input('search_title'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->input('search_author'), function ($query, $search) {
                $query->where('author', 'like', "%{$search}%");
            })
            ->when($request->input('search_isbn'), function ($query, $search) {
                $query->where('isbn', 'like', "%{$search}%");
            })
            ->when($request->input('search_status'), function ($query, $status) {
                if ($status === 'available') {
                    $query->whereDoesntHave('rentals', function ($q) {
                        $q->whereNull('returned_at');
                    });
                } elseif ($status === 'rented') {
                    $query->whereHas('rentals', function ($q) {
                        $q->whereNull('returned_at');
                    });
                }
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'is_available' => $book->isAvailable(),
            ]);

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => $request->only(['search_title', 'search_author', 'search_isbn', 'search_status', 'per_page']),
        ]);
    }

    public function show(Book $book)
    {
        $book->load(['rentals.user', 'currentRental.user']);

        return Inertia::render('Books/Show', [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'is_available' => $book->isAvailable(),
                'current_rental' => $book->currentRental ? [
                    'user_name' => $book->currentRental->user->name,
                    'rented_at' => $book->currentRental->rented_at,
                    'due_date' => $book->currentRental->due_date,
                ] : null,
                'history' => $book->rentals->map(function ($rental) {
                    return [
                        'user_name' => $rental->user->name,
                        'rented_at' => $rental->rented_at,
                        'returned_at' => $rental->returned_at,
                    ];
                }),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Books/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
        ]);

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Book created successfully');
    }
}
