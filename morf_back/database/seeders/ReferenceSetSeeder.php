<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceSetSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $titles = [
            'Week 1: Portraits & Emotions',
            'Week 2: Urban Landscapes',
            'Week 3: Fantasy Characters',
            'Week 4: Architectural Wonders',
        ];

        foreach ($titles as $index => $title) {
            $isPublished = $index < 3;
            ReferenceSet::create([
                'id' => Str::uuid7()->toString(),
                'title' => $title,
                'week_start_date' => now()->subWeeks(count($titles) - $index - 1)->startOfWeek(),
                'is_published' => $isPublished,
                'published_at' => $isPublished ? now()->subWeeks(count($titles) - $index - 1) : null,
                'created_by' => $users->random()->id,
            ]);
        }
    }
}
