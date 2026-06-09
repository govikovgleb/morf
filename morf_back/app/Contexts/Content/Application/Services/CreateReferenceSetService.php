<?php

declare(strict_types=1);

namespace App\Contexts\Content\Application\Services;

use App\Contexts\Content\Domain\ReferenceSet;
use Carbon\Carbon;

class CreateReferenceSetService
{
    public function execute(?string $title = null, ?Carbon $weekStartDate = null, ?string $createdBy = null): ReferenceSet
    {
        return ReferenceSet::create([
            'title' => $title,
            'week_start_date' => $weekStartDate,
            'created_by' => $createdBy,
        ]);
    }
}
