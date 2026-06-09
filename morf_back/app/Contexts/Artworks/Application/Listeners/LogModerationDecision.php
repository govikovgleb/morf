<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Listeners;

use App\Contexts\Artworks\Domain\Events\ArtworkModerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogModerationDecision implements ShouldQueue
{
    public function handle(ArtworkModerated $event): void
    {
        Log::info('Artwork moderated', [
            'artwork_id' => $event->artwork->id,
            'action' => $event->action,
            'reason' => $event->reason,
            'status' => $event->artwork->status,
        ]);
    }
}
