<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain\Events;

use App\Contexts\Content\Domain\ReferenceSet;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReferenceSetPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly ReferenceSet $referenceSet)
    {
    }
}
