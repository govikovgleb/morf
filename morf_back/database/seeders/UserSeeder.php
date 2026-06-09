<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'id' => Str::uuid7()->toString(),
            'public_nickname' => 'admin',
            'email' => 'admin@morf.app',
            'role' => 'admin',
            'auth_hash' => hash('sha256', 'admin-device-token'),
            'recovery_code_hash' => null,
            'password' => Hash::make('admin123'),
        ]);

        // Regular users
        $nicknames = ['artist_01', 'sketcher', 'paint_master', 'doodle_king', 'canvas_queen'];
        foreach ($nicknames as $nickname) {
            User::create([
                'id' => Str::uuid7()->toString(),
                'public_nickname' => $nickname,
                'email' => null,
                'role' => 'user',
                'auth_hash' => hash('sha256', $nickname.'-token'),
                'recovery_code_hash' => null,
                'password' => null,
            ]);
        }
    }
}
