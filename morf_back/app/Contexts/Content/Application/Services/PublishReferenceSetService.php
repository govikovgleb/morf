<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Services;

use App\Contexts\Content\Domain\ReferenceSet;
use Carbon\Carbon;

class PublishReferenceSetService
{
    public function execute(ReferenceSet $set): void
    {
        $set->update([
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);
    }
}
