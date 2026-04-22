<?php

namespace Tests\Feature;

use Database\Seeders\BookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_can_be_run_multiple_times()
    {
        $this->seed(BookSeeder::class);

        // This should not throw UniqueConstraintViolationException
        $this->seed(BookSeeder::class);

        $this->assertTrue(true);
    }
}
