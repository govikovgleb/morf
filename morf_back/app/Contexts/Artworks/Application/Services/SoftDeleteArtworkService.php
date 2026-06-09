<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Services;

use App\Contexts\Artworks\Domain\Artwork;

class SoftDeleteArtworkService
{
    public function execute(string $artworkId): void
    {
        $artwork = Artwork::findOrFail($artworkId);
        $artwork->delete();
    }
}
