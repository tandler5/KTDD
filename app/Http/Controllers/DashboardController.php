<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        if ($isAdmin) {
            $statistics = [
                'total_active_rentals' => Rental::whereNull('returned_at')->count(),
                'total_users' => User::count(),
                'total_books' => Book::count(),
                'most_rented_book' => $this->getMostRentedBook(),
            ];
        } else {
            $statistics = [
                'active_rentals_count' => $user->activeRentals()->count(),
                'total_rentals_count' => $user->rentals()->count(),
                'most_rented_book' => $this->getUserMostRentedBook($user),
            ];
        }

        return Inertia::render('Dashboard', [
            'isAdministrator' => $isAdmin,
            'statistics' => $statistics,
            'usersLink' => $isAdmin ? route('users.index') : null,
            'booksLink' => route('books.index'),
        ]);
    }

    private function getMostRentedBook(): ?array
    {
        $book = Book::withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->first();

        return $book ? [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'rental_count' => $book->rentals_count,
        ] : null;
    }

    private function getUserMostRentedBook($user): ?array
    {
        $book = Book::whereHas('rentals', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->withCount(['rentals' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('rentals_count', 'desc')
            ->first();

        return $book ? [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'rental_count' => $book->rentals_count,
        ] : null;
    }
}
