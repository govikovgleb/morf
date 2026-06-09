<?php

namespace Tests\Feature\Api;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceSetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_published_reference_sets(): void
    {
        $user = User::factory()->create();
        ReferenceSet::factory()->count(3)->create([
            'is_published' => true,
            'created_by' => $user->id,
        ]);
        ReferenceSet::factory()->create([
            'is_published' => false,
            'created_by' => $user->id,
        ]);

        $response = $this->getJson('/api/reference-sets');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_show_single_reference_set(): void
    {
        $user = User::factory()->create();
        $set = ReferenceSet::factory()->create([
            'is_published' => true,
            'created_by' => $user->id,
        ]);

        $response = $this->getJson("/api/reference-sets/{$set->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $set->id]);
    }
}
