<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Content\Domain\ReferenceCategory;
use App\Contexts\Content\Domain\ReferenceImage;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceImageSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ReferenceCategory::all()->keyBy('slug');
        $users = User::where('role', 'user')->get();
        $realArtPath = storage_path('real_art');
        $weekDirs = glob($realArtPath.'/*', GLOB_ONLYDIR);

        foreach ($weekDirs as $weekDir) {
            $refDir = $weekDir.'/ref';
            if (! is_dir($refDir)) {
                continue;
            }

            $files = glob($refDir.'/*');
            foreach ($files as $file) {
                if (! is_file($file)) {
                    continue;
                }

                $slug = strtolower(pathinfo($file, PATHINFO_FILENAME));
                $category = $categories->get($slug);
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
            }
        }
    }
}
