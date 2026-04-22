<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookCreateAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_access_book_create_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('books.create'))
            ->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_create_book(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post(route('books.store'), [
                'title' => 'New Book',
                'author' => 'Author',
                'isbn' => '123-456-789',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('books', ['title' => 'New Book']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function administrator_can_create_book(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $response = $this->actingAs($admin)
            ->post(route('books.store'), [
                'title' => 'New Admin Book',
                'author' => 'Admin Author',
                'isbn' => '999-888-777',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['title' => 'New Admin Book']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function administrator_can_access_create_page(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $response = $this->actingAs($admin)->get(route('books.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Books/Create')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function books_index_shows_create_button_only_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Admin should have canCreate prop
        $adminResponse = $this->actingAs($admin)->get(route('books.index'));
        $adminResponse->assertInertia(fn (Assert $page) => $page
            ->where('canCreate', true)
        );

        // Customer should not have canCreate or it should be false
        $customerResponse = $this->actingAs($customer)->get(route('books.index'));
        $customerResponse->assertInertia(fn (Assert $page) => $page
            ->where('canCreate', false)
        );
    }
}
