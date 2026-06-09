<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Listeners;

use App\Contexts\Artworks\Domain\Events\ArtworkSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogArtworkSubmission implements ShouldQueue
{
    public function handle(ArtworkSubmitted $event): void
    {
        Log::info('Artwork submitted', [
            'artwork_id' => $event->artwork->id,
            'user_id' => $event->artwork->user_id,
            'reference_set_id' => $event->artwork->reference_set_id,
        ]);
    }
}
