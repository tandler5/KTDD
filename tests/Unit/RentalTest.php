<?php

namespace Tests\Unit;

use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalTest extends TestCase
{    
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_rental()
    {
        $rental = Rental::create([
            'book_id' => 1,
            'user_id' => 1,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->assertDatabaseHas('rentals', [
            'book_id' => 1,
            'user_id' => 1,
            'rented_at' => $rental->rented_at,
            'due_date' => $rental->due_date,
        ]);
    }
}
