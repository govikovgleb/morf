<?php

namespace Database\Factories;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReferenceSetFactory extends Factory
{
    protected $model = ReferenceSet::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid7()->toString(),
            'title' => fake()->sentence(3),
            'week_start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'is_published' => fake()->boolean(),
            'published_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'created_by' => User::factory(),
        ];
    }
}
