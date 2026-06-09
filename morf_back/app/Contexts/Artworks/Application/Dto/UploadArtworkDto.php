<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Dto;

use Illuminate\Http\UploadedFile;

class UploadArtworkDto
{
    public function __construct(
        public readonly string $userId,
        public readonly string $referenceSetId,
        public readonly UploadedFile $file,
        public readonly ?string $caption = null,
    ) {}
}
