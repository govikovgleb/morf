<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Engagement\Domain\Like;
use App\Contexts\Identity\Domain\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $artworks = Artwork::approved()->get();

        foreach ($artworks as $artwork) {
            // 0-5 likes per artwork
            $likeCount = rand(0, min(5, $users->count()));
            $likers = $users->shuffle()->take($likeCount);

            foreach ($likers as $user) {
                Like::create([
                    'id' => Str::uuid7()->toString(),
                    'artwork_id' => $artwork->id,
                    'user_id' => $user->id,
                ]);
            }

            // Sync likes_count
            $actualLikes = Like::where('artwork_id', $artwork->id)->count();
            $artwork->update(['likes_count' => $actualLikes]);
        }
    }
}
