<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $users = User::query()
            ->when($request->input('search_name'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->input('search_email'), function ($query, $search) {
                $query->where('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'active_rentals_count' => $user->activeRentals()->count(),
            ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search_name', 'search_email', 'per_page']),
        ]);
    }

    public function show(User $user, Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $user->load(['activeRentals.book']);

        $rentals = $user->rentals()
            ->with('book')
            ->when($request->input('search_book'), function ($query, $search) {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($request->input('rented_from'), function ($query, $date) {
                $query->whereDate('rented_at', '>=', $date);
            })
            ->when($request->input('rented_to'), function ($query, $date) {
                $query->whereDate('rented_at', '<=', $date);
            })
            ->when($request->input('returned_from'), function ($query, $date) {
                $query->whereDate('returned_at', '>=', $date);
            })
            ->when($request->input('returned_to'), function ($query, $date) {
                $query->whereDate('returned_at', '<=', $date);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($rental) => [
                'id' => $rental->id,
                'book_id' => $rental->book_id,
                'book_title' => $rental->book->title,
                'rented_at' => $rental->rented_at,
                'returned_at' => $rental->returned_at,
            ]);

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'active_rentals' => $user->activeRentals->map(fn ($rental) => [
                    'id' => $rental->id,
                    'book_title' => $rental->book->title,
                    'rented_at' => $rental->rented_at,
                    'due_date' => $rental->due_date,
                ]),
            ],
            'rentals' => $rentals,
            'filters' => $request->only(['search_book', 'rented_from', 'rented_to', 'returned_from', 'returned_to', 'per_page']),
        ]);
    }
}
