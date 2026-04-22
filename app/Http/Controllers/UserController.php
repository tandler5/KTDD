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

    public function show(User $user)
    {
        $user->load(['activeRentals.book']);

        $rentals = $user->rentals()
            ->with('book')
            ->latest()
            ->paginate(10)
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
        ]);
    }
}
