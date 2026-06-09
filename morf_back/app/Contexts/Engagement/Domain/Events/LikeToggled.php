<?php

declare(strict_types=1);

namespace App\Contexts\Engagement\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LikeToggled
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $artworkId,
        public readonly string $userId,
        public readonly bool $liked,
        public readonly int $likesCount,
    ) {
    }
}
