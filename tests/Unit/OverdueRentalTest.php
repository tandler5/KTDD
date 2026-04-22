<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OverdueRentalTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_rent_if_has_overdue_rentals()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Vytvoříme výpůjčku, která je už po termínu
        Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(20),
            'due_date' => now()->subDays(6), // Termín byl před 6 dny
        ]);

        $this->assertTrue($user->hasOverdueRentals());
        $this->assertFalse($user->canRentMoreBooks());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_rent_if_rentals_are_not_overdue()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Výpůjčka v termínu
        Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(5),
            'due_date' => now()->addDays(9),
        ]);

        $this->assertFalse($user->hasOverdueRentals());
        $this->assertTrue($user->canRentMoreBooks());
    }
}
