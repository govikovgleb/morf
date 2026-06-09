<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Listeners;

use App\Contexts\Content\Domain\Events\ReferenceSetPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearPublishedSetsCache implements ShouldQueue
{
    public function handle(ReferenceSetPublished $event): void
    {
        Cache::forget('reference_sets:latest');
        Cache::forget('reference_sets:' . $event->referenceSet->id);

        Log::info('Reference set published, cache cleared', [
            'set_id' => $event->referenceSet->id,
            'title' => $event->referenceSet->title,
        ]);
    }
}
