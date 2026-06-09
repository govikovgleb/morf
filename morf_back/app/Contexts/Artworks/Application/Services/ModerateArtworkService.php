<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Services;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Artworks\Domain\Events\ArtworkModerated;
use Carbon\Carbon;

class ModerateArtworkService
{
    public function execute(string $artworkId, string $action, string $actorId, ?string $reason = null): Artwork
    {
        $artwork = Artwork::findOrFail($artworkId);

        $status = match ($action) {
            'approve' => 'approved',
            'reject' => 'rejected',
            default => throw new \InvalidArgumentException("Invalid action: {$action}"),
        };

        $artwork->update([
            'status' => $status,
            'moderated_by' => $actorId,
            'moderated_at' => Carbon::now(),
        ]);

        ArtworkModerated::dispatch($artwork, $action, $reason);

        return $artwork;
    }
}
