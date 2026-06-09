<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_anonymously(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nickname' => 'test_artist',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token']);
    }

    public function test_registration_requires_nickname(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname']);
    }

    public function test_registration_rejects_short_nickname(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nickname' => 'ab',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname']);
    }
}
