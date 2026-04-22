<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RentalLinksTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function rental_index_contains_book_and_user_ids_for_linking()
    {
        $admin = User::factory()->create(['role' => 'administrator', 'name' => 'Admin']);
        $borrower = User::factory()->create(['name' => 'John Doe', 'role' => 'customer']);
        $book = Book::factory()->create(['title' => 'Laravel Guide']);

        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('rentals.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data.0', fn (Assert $rental) => $rental
                ->has('book_id')
                ->has('user_id')
                ->where('book_title', 'Laravel Guide')
                ->where('user_name', 'John Doe')
                ->etc()
            )
        );
    }
}
