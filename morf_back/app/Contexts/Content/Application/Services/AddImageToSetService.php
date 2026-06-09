<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Services;

use App\Contexts\Content\Domain\ReferenceSetItem;

class AddImageToSetService
{
    public function execute(string $setId, string $imageId): ReferenceSetItem
    {
        return ReferenceSetItem::create([
            'set_id' => $setId,
            'reference_image_id' => $imageId,
        ]);
    }
}
