<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function health_endpoint_returns_ok_when_database_is_healthy()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => ['database'],
            ])
            ->assertJsonFragment(['status' => 'ok']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function health_endpoint_does_not_require_authentication()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
    }
}
