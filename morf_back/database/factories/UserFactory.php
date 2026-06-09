<?php

namespace Database\Factories;

use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid7()->toString(),
            'public_nickname' => fake()->userName(),
            'role' => 'user',
            'auth_hash' => hash('sha256', fake()->uuid()),
            'recovery_code_hash' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
