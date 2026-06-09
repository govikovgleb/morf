<?php

declare(strict_types=1);

namespace App\Contexts\Engagement\Application\Listeners;

use App\Contexts\Engagement\Domain\Events\LikeToggled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogLikeActivity implements ShouldQueue
{
    public function handle(LikeToggled $event): void
    {
        Log::info('Like toggled', [
            'artwork_id' => $event->artworkId,
            'user_id' => $event->userId,
            'liked' => $event->liked,
            'likes_count' => $event->likesCount,
        ]);
    }
}
