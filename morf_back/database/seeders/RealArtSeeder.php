<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Content\Domain\ReferenceCategory;
use App\Contexts\Content\Domain\ReferenceImage;
use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Content\Domain\ReferenceSetItem;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealArtSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $realArtPath = storage_path('real_art');
        $weekDirs = glob($realArtPath.'/*', GLOB_ONLYDIR);

        $categoryMap = $this->seedCategories();

        foreach ($weekDirs as $weekDir) {
            $weekName = basename($weekDir);
            $title = ucfirst(str_replace('week_', '', $weekName));

            if ($title === 'Jellyfish') {
                $weekStart = now()->subWeek()->startOfWeek();
                $publishedAt = now()->subWeek();
            } else {
                $weekStart = now()->startOfWeek();
                $publishedAt = now();
            }

            $set = ReferenceSet::create([
                'id' => Str::uuid7()->toString(),
                'title' => $title,
                'week_start_date' => $weekStart,
                'is_published' => true,
                'published_at' => $publishedAt,
                'created_by' => $users->random()->id,
            ]);

            $refDir = $weekDir.'/ref';
            $imageIds = [];

            if (is_dir($refDir)) {
                foreach (glob($refDir.'/*') as $file) {
                    if (! is_file($file)) {
                        continue;
                    }

                    $slug = strtolower(pathinfo($file, PATHINFO_FILENAME));
                    $category = $categoryMap[$slug] ?? null;
                    if (! $category) {
                        continue;
                    }

                    $id = Str::uuid7()->toString();
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $destPath = "reference_images/{$id}.{$ext}";
                    $absDest = storage_path('app/public/'.$destPath);

                    if (! is_dir(dirname($absDest))) {
                        mkdir(dirname($absDest), 0755, true);
                    }

                    copy($file, $absDest);

                    [$width, $height] = getimagesize($file) ?: [null, null];

                    ReferenceImage::create([
                        'id' => $id,
                        'category_id' => $category->id,
                        'cdn_url' => config('app.url').'/storage/'.$destPath,
                        'storage_path' => $destPath,
                        'width' => $width,
                        'height' => $height,
                        'file_size_bytes' => filesize($absDest),
                        'mime_type' => mime_content_type($absDest),
                        'uploaded_by' => $users->random()->id,
                    ]);

                    $imageIds[] = $id;
                }
            }

            foreach ($imageIds as $imageId) {
                ReferenceSetItem::create([
                    'id' => Str::uuid7()->toString(),
                    'set_id' => $set->id,
                    'reference_image_id' => $imageId,
                ]);
            }

            $artDir = $weekDir.'/art';

            if (is_dir($artDir)) {
                foreach (glob($artDir.'/*') as $file) {
                    if (! is_file($file)) {
                        continue;
                    }

                    $id = Str::uuid7()->toString();
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $destPath = "artworks/{$id}.{$ext}";
                    $absDest = storage_path('app/public/'.$destPath);

                    if (! is_dir(dirname($absDest))) {
                        mkdir(dirname($absDest), 0755, true);
                    }

                    copy($file, $absDest);

                    [$width, $height] = getimagesize($file) ?: [null, null];
                    $user = $users->random();

                    Artwork::create([
                        'id' => $id,
                        'user_id' => $user->id,
                        'reference_set_id' => $set->id,
                        'status' => 'approved',
                        'caption' => null,
                        'cdn_url' => config('app.url').'/storage/'.$destPath,
                        'storage_path' => $destPath,
                        'width' => $width,
                        'height' => $height,
                        'file_size_bytes' => filesize($absDest),
                        'mime_type' => mime_content_type($absDest),
                        'likes_count' => 0,
                        'author_nickname' => $user->public_nickname,
                    ]);
                }
            }
        }
    }

    private function seedCategories(): array
    {
        $categories = [
            ['name' => 'Environment', 'slug' => 'environment', 'sort_order' => 1],
            ['name' => 'Design', 'slug' => 'design', 'sort_order' => 2],
            ['name' => 'Character', 'slug' => 'character', 'sort_order' => 3],
        ];

        $map = [];
        foreach ($categories as $data) {
            $cat = ReferenceCategory::create([
                'id' => Str::uuid7()->toString(),
                ...$data,
            ]);
            $map[$cat->slug] = $cat;
        }

        return $map;
    }
}
