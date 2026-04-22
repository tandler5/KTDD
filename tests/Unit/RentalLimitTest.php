<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalLimitTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_rent_more_than_three_books()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create();

        // Půjčíme 3 knihy
        foreach ($books as $book) {
            Rental::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'rented_at' => now(),
                'due_date' => now()->addDays(14),
            ]);
        }

        // Čtvrtá kniha
        $fourthBook = Book::factory()->create();

        // Zde budeme očekávat výjimku nebo false z nějaké doménové metody
        // Pro TDD začneme s metodou canRent() na modelu User
        $this->assertFalse($user->canRentMoreBooks());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_rent_up_to_three_books()
    {
        $user = User::factory()->create();

        $this->assertTrue($user->canRentMoreBooks(), 'User should be able to rent when having 0 books');

        Book::factory()->count(2)->create()->each(function ($book) use ($user) {
            Rental::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'rented_at' => now(),
                'due_date' => now()->addDays(14),
            ]);
        });

        $this->assertTrue($user->canRentMoreBooks(), 'User should be able to rent when having 2 books');
    }
}
