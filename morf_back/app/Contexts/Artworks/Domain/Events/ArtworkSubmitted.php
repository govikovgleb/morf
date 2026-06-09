<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Domain\Events;

use App\Contexts\Artworks\Domain\Artwork;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArtworkSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Artwork $artwork)
    {
    }
}
