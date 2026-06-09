<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain\Observers;

use App\Contexts\Content\Domain\Events\ReferenceSetPublished;
use App\Contexts\Content\Domain\ReferenceSet;

class ReferenceSetObserver
{
    public function updated(ReferenceSet $referenceSet): void
    {
        if ($referenceSet->wasChanged('is_published') && $referenceSet->is_published) {
            ReferenceSetPublished::dispatch($referenceSet);
        }
    }
}
