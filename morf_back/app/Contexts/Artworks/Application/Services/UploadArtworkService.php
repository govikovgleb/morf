<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Application\Services;

use App\Contexts\Artworks\Application\Dto\UploadArtworkDto;
use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Artworks\Domain\Events\ArtworkSubmitted;
use App\Contexts\Identity\Domain\User;
use Illuminate\Support\Facades\Storage;

class UploadArtworkService
{
    public function execute(UploadArtworkDto $dto): Artwork
    {
        $path = $dto->file->store('artworks', 's3');
        $cdnUrl = Storage::disk('s3')->url($path);

        [$width, $height] = getimagesize($dto->file->getRealPath()) ?: [null, null];

        $user = User::find($dto->userId);

        $artwork = Artwork::create([
            'user_id' => $dto->userId,
            'reference_set_id' => $dto->referenceSetId,
            'cdn_url' => $cdnUrl,
            'storage_path' => $path,
            'width' => $width,
            'height' => $height,
            'file_size_bytes' => $dto->file->getSize(),
            'mime_type' => $dto->file->getMimeType(),
            'caption' => $dto->caption,
            'author_nickname' => $user?->public_nickname,
            'status' => 'pending',
        ]);

        ArtworkSubmitted::dispatch($artwork);

        return $artwork;
    }
}
