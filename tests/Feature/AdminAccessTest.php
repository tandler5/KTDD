<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_access_user_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherUser = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer);

        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('users.show', $otherUser))->assertForbidden();
        $this->patch(route('users.update-role', $otherUser), ['role' => 'administrator'])->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_access_rentals_index(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('rentals.index'))
            ->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_does_not_see_book_rental_history(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book = Book::factory()->create();

        Rental::factory()->create([
            'book_id' => $book->id,
            'user_id' => $borrower->id,
        ]);

        $response = $this->actingAs($customer)->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Books/Show')
            ->where('canViewRentalHistory', false)
            ->has('rentals.data', 0)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function administrator_can_access_restricted_pages_and_history(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book = Book::factory()->create();

        Rental::factory()->create([
            'book_id' => $book->id,
            'user_id' => $borrower->id,
        ]);

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('rentals.index'))->assertOk();

        $bookShowResponse = $this->actingAs($admin)->get(route('books.show', $book));
        $bookShowResponse->assertStatus(200);
        $bookShowResponse->assertInertia(fn (Assert $page) => $page
            ->where('canViewRentalHistory', true)
            ->has('rentals.data', 1)
        );
    }
}
