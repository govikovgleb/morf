<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Static\Domain\ProjectInfo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectInfoSeeder extends Seeder
{
    public function run(): void
    {
        ProjectInfo::create([
            'id' => Str::uuid7()->toString(),
            'key' => 'welcome_text',
            'value' => json_encode([
                'title' => 'Welcome to Morf!',
                'body' => 'Join our weekly art challenges. Create characters based on reference sets and share with the community.',
            ]),
        ]);

        ProjectInfo::create([
            'id' => Str::uuid7()->toString(),
            'key' => 'rules',
            'value' => json_encode([
                'Be respectful to all artists.',
                'Original artwork only.',
                'Have fun and experiment!',
            ]),
        ]);

        ProjectInfo::create([
            'id' => Str::uuid7()->toString(),
            'key' => 'contact_email',
            'value' => json_encode('hello@morf.app'),
        ]);
    }
}
