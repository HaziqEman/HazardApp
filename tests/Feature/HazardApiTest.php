<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HazardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_hazards(): void
    {
        $response = $this->getJson('/api/hazards');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'username',
                    'category',
                    'description',
                    'latitude',
                    'longitude',
                    'device_info',
                    'reported_at',
                ],
            ]);
    }

    public function test_can_create_hazard_with_validation_errors(): void
    {
        $response = $this->postJson('/api/hazards', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }
}
