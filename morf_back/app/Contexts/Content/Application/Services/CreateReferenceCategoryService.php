<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Services;

use App\Contexts\Content\Domain\ReferenceCategory;

class CreateReferenceCategoryService
{
    public function execute(string $name, string $slug, int $sortOrder = 0): ReferenceCategory
    {
        return ReferenceCategory::create([
            'name' => $name,
            'slug' => $slug,
            'sort_order' => $sortOrder,
        ]);
    }
}
