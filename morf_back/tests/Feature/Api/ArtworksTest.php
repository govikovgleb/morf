<?php

namespace Tests\Feature\Api;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworksTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_approved_artworks(): void
    {
        $user = User::factory()->create();
        $set = ReferenceSet::factory()->create(['is_published' => true]);
        Artwork::factory()->count(3)->create([
            'status' => 'approved',
            'user_id' => $user->id,
            'reference_set_id' => $set->id,
        ]);
        Artwork::factory()->create([
            'status' => 'pending',
            'user_id' => $user->id,
            'reference_set_id' => $set->id,
        ]);

        $response = $this->getJson('/api/artworks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_show_single_approved_artwork(): void
    {
        $user = User::factory()->create();
        $set = ReferenceSet::factory()->create(['is_published' => true]);
        $artwork = Artwork::factory()->create([
            'status' => 'approved',
            'user_id' => $user->id,
            'reference_set_id' => $set->id,
        ]);

        $response = $this->getJson("/api/artworks/{$artwork->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $artwork->id]);
    }
}
