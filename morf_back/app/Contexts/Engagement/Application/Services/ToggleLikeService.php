<?php

declare(strict_types=1);

namespace App\Contexts\Engagement\Application\Services;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Engagement\Domain\Events\LikeToggled;
use App\Contexts\Engagement\Domain\Like;
use Illuminate\Support\Facades\DB;

class ToggleLikeService
{
    /**
     * @return array{added: bool, likes_count: int}
     */
    public function execute(string $artworkId, string $userId): array
    {
        return DB::transaction(function () use ($artworkId, $userId) {
            // Lock artwork row to prevent race condition on likes_count
            $artwork = Artwork::lockForUpdate()->findOrFail($artworkId);

            $like = Like::where('artwork_id', $artworkId)
                ->where('user_id', $userId)
                ->first();

            if ($like) {
                $like->delete();
                $artwork->decrement('likes_count');
                $added = false;
            } else {
                Like::create([
                    'artwork_id' => $artworkId,
                    'user_id' => $userId,
                ]);
                $artwork->increment('likes_count');
                $added = true;
            }

            // Refresh to get actual value after increment/decrement
            $artwork->refresh();

            $result = [
                'added' => $added,
                'likes_count' => $artwork->likes_count,
            ];

            LikeToggled::dispatch($artworkId, $userId, $added, $artwork->likes_count);

            return $result;
        });
    }
}
