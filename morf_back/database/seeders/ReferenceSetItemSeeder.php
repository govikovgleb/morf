<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Content\Domain\ReferenceImage;
use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Content\Domain\ReferenceSetItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceSetItemSeeder extends Seeder
{
    public function run(): void
    {
        $sets = ReferenceSet::all();
        $images = ReferenceImage::all();

        foreach ($sets as $set) {
            // Attach 4-6 random images to each set
            $setImages = $images->random(min(6, $images->count()));
            foreach ($setImages as $index => $image) {
                ReferenceSetItem::create([
                    'id' => Str::uuid7()->toString(),
                    'set_id' => $set->id,
                    'reference_image_id' => $image->id,

                ]);
            }
        }
    }
}
