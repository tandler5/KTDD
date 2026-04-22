<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RentalFilterTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_rentals_by_date_and_status()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book1 = Book::factory()->create(['title' => 'Book A']);
        $book2 = Book::factory()->create(['title' => 'Book B']);

        // Active rental
        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book1->id,
            'rented_at' => '2023-01-01 10:00:00',
            'due_date' => '2023-01-15 10:00:00',
            'returned_at' => null,
        ]);

        // Returned rental
        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book2->id,
            'rented_at' => '2023-02-01 10:00:00',
            'due_date' => '2023-02-15 10:00:00',
            'returned_at' => '2023-02-10 10:00:00',
        ]);

        $this->actingAs($admin);

        // Filter by rented_at date
        $response = $this->get(route('rentals.index', ['search_rented' => '2023-01-01']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Book A')
        );

        // Filter by due_date
        $response = $this->get(route('rentals.index', ['search_due' => '2023-02-15']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Book B')
        );

        // Filter by status (returned)
        $response = $this->get(route('rentals.index', ['search_status' => 'returned']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Book B')
        );

        // Filter by date range (rented_from/to)
        $response = $this->get(route('rentals.index', ['rented_from' => '2023-01-15', 'rented_to' => '2023-02-15']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Book B')
        );

        // Filter by due date range (due_from/to)
        $response = $this->get(route('rentals.index', ['due_from' => '2023-01-01', 'due_to' => '2023-01-20']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Book A')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_rentals_by_book_title_and_user_name()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $borrower = User::factory()->create(['name' => 'John Doe', 'role' => 'customer']);
        $book1 = Book::factory()->create(['title' => 'Laravel Guide']);
        $book2 = Book::factory()->create(['title' => 'Vue Guide']);

        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book1->id,
            'rented_at' => '2023-01-01 10:00:00',
            'due_date' => '2023-01-15 10:00:00',
            'returned_at' => null,
        ]);

        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book2->id,
            'rented_at' => '2023-02-01 10:00:00',
            'due_date' => '2023-02-15 10:00:00',
            'returned_at' => null,
        ]);

        $this->actingAs($admin);

        // Filter by book title
        $response = $this->get(route('rentals.index', ['search_book' => 'Laravel']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Laravel Guide')
        );

        // Filter by user name
        $response = $this->get(route('rentals.index', ['search_user' => 'John']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 2)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_rentals_by_overdue_status()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book = Book::factory()->create(['title' => 'Overdue Book']);

        // Overdue rental (past due date, not returned)
        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book->id,
            'rented_at' => '2020-01-01 10:00:00',
            'due_date' => '2020-01-15 10:00:00',
            'returned_at' => null,
        ]);

        $this->actingAs($admin);

        // Filter by overdue status
        $response = $this->get(route('rentals.index', ['search_status' => 'overdue']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Overdue Book')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_rentals_by_active_status()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book1 = Book::factory()->create(['title' => 'Active Book']);
        $book2 = Book::factory()->create(['title' => 'Returned Book']);

        // Active rental (not returned)
        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book1->id,
            'rented_at' => '2023-01-01 10:00:00',
            'due_date' => '2023-01-15 10:00:00',
            'returned_at' => null,
        ]);

        // Returned rental
        Rental::create([
            'user_id' => $borrower->id,
            'book_id' => $book2->id,
            'rented_at' => '2023-02-01 10:00:00',
            'due_date' => '2023-02-15 10:00:00',
            'returned_at' => '2023-02-10 10:00:00',
        ]);

        $this->actingAs($admin);

        // Filter by active status (not returned)
        $response = $this->get(route('rentals.index', ['search_status' => 'active']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 1)
            ->where('rentals.data.0.book_title', 'Active Book')
        );
    }
}
