<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Content\Domain\ReferenceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Environment', 'slug' => 'environment', 'sort_order' => 1],
            ['name' => 'Design', 'slug' => 'design', 'sort_order' => 2],
            ['name' => 'Character', 'slug' => 'character', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            ReferenceCategory::create([
                'id' => Str::uuid7()->toString(),
                ...$category,
            ]);
        }
    }
}
