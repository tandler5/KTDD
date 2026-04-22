<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_detail_route_has_correct_name_for_wildcard_matching()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get(route('books.show', $book));

        $response->assertStatus(200);
        $this->assertEquals('books.show', request()->route()->getName());
        $this->assertStringStartsWith('books.', request()->route()->getName());
    }

    public function test_user_detail_route_has_correct_name_for_wildcard_matching()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $otherUser = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->get(route('users.show', $otherUser));

        $response->assertStatus(200);
        $this->assertEquals('users.show', request()->route()->getName());
        $this->assertStringStartsWith('users.', request()->route()->getName());
    }
}
