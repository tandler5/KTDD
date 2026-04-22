<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class RentalServiceTest extends TestCase
{
    use RefreshDatabase;

    private RentalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RentalService();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_successfully_rents_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $rental = $this->service->rentBook($user, $book);

        $this->assertInstanceOf(Rental::class, $rental);
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_to_rent_if_book_is_not_available()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::factory()->create();

        $this->service->rentBook($user1, $book);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Book is already rented');

        $this->service->rentBook($user2, $book);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_to_rent_if_user_reached_limit()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create();

        foreach ($books as $book) {
            $this->service->rentBook($user, $book);
        }

        $anotherBook = Book::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User cannot rent more books');

        $this->service->rentBook($user, $anotherBook);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_successfully_returns_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $rental = $this->service->rentBook($user, $book);

        $this->service->returnBook($book);

        $this->assertNotNull($rental->fresh()->returned_at);
        $this->assertTrue($book->fresh()->isAvailable());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_to_return_a_book_that_is_not_rented()
    {
        $book = Book::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Book is not currently rented');

        $this->service->returnBook($book);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_idempotent_when_renting_the_same_book_again()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $rental1 = $this->service->rentBook($user, $book);
        $rental2 = $this->service->rentBook($user, $book);

        $this->assertEquals($rental1->id, $rental2->id);
        $this->assertEquals(1, Rental::count());
    }
}
