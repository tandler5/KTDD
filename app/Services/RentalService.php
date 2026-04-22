<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Rental;
use Exception;

class RentalService
{
    /**
     * Rent a book to a user.
     *
     * @throws Exception
     */
    public function rentBook(User $user, Book $book): Rental
    {
        // Idempotence: check if this user already has this book rented
        $existingRental = Rental::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($existingRental) {
            return $existingRental;
        }

        if (!$book->isAvailable()) {
            throw new Exception('Book is already rented');
        }

        if (!$user->canRentMoreBooks()) {
            throw new Exception('User cannot rent more books');
        }

        return Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);
    }

    /**
     * Return a book.
     *
     * @throws Exception
     */
    public function returnBook(Book $book): void
    {
        $activeRental = $book->rentals()->whereNull('returned_at')->first();

        if (!$activeRental) {
            throw new Exception('Book is not currently rented');
        }

        $activeRental->update([
            'returned_at' => now(),
        ]);
    }
}
