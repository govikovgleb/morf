<?php

namespace Database\Factories;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtworkFactory extends Factory
{
    protected $model = Artwork::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid7()->toString(),
            'user_id' => User::factory(),
            'reference_set_id' => ReferenceSet::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'caption' => fake()->optional()->sentence(),
            'cdn_url' => fake()->imageUrl(800, 800),
            'storage_path' => fake()->filePath(),
            'width' => 800,
            'height' => 800,
            'file_size_bytes' => fake()->numberBetween(10000, 50000),
            'mime_type' => 'image/png',
            'likes_count' => fake()->numberBetween(0, 100),
            'author_nickname' => fake()->userName(),
        ];
    }
}
