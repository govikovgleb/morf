<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Services;

use App\Contexts\Content\Domain\ReferenceImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadReferenceImageService
{
    public function execute(UploadedFile $file, string $categoryId, ?string $uploadedBy = null): ReferenceImage
    {
        $path = $file->store('references', 's3');
        $cdnUrl = Storage::disk('s3')->url($path);

        [$width, $height] = getimagesize($file->getRealPath()) ?: [null, null];

        return ReferenceImage::create([
            'category_id' => $categoryId,
            'cdn_url' => $cdnUrl,
            'storage_path' => $path,
            'width' => $width,
            'height' => $height,
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $uploadedBy,
        ]);
    }
}
