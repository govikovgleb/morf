<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Database\Seeders\Support\PlaceholderImageGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArtworkSeeder extends Seeder
{
    public function run(): void
    {
        $generator = new PlaceholderImageGenerator;
        $users = User::where('role', 'user')->get();
        $sets = ReferenceSet::where('is_published', true)->get();

        $statuses = ['pending', 'approved', 'rejected'];
        $statusWeights = [0.2, 0.7, 0.1]; // 20% pending, 70% approved, 10% rejected

        foreach ($sets as $set) {
            // 5-10 artworks per set
            $artworkCount = rand(5, 10);
            for ($i = 0; $i < $artworkCount; $i++) {
                $user = $users->random();
                $id = Str::uuid7()->toString();
                $status = $this->weightedRandom($statuses, $statusWeights);
                $cdnUrl = $generator->generateArtworkImage($id, 800, 800);

                Artwork::create([
                    'id' => $id,
                    'user_id' => $user->id,
                    'reference_set_id' => $set->id,
                    'status' => $status,
                    'caption' => $this->randomCaption(),
                    'cdn_url' => $cdnUrl,
                    'storage_path' => "artworks/{$id}.png",
                    'width' => 800,
                    'height' => 800,
                    'file_size_bytes' => 20480,
                    'mime_type' => 'image/png',
                    'likes_count' => rand(0, 50),
                    'author_nickname' => $user->public_nickname,
                ]);
            }
        }
    }

    private function weightedRandom(array $items, array $weights): string
    {
        $total = array_sum($weights);
        $random = mt_rand() / mt_getrandmax() * $total;
        $current = 0;

        foreach ($items as $index => $item) {
            $current += $weights[$index];
            if ($random <= $current) {
                return $item;
            }
        }

        return $items[0];
    }

    private function randomCaption(): ?string
    {
        $captions = [
            'My interpretation of this week\'s theme!',
            'Had so much fun with this one.',
            'Experimenting with new techniques.',
            'Hope you all like it!',
            'Took me hours but worth it.',
            'Quick sketch before bed.',
            null,
            null,
        ];

        return $captions[array_rand($captions)];
    }
}
