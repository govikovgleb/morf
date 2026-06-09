<?php

namespace App\Filament\Resources\ModerationActions\Pages;

use App\Filament\Resources\ModerationActions\ModerationActionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModerationAction extends CreateRecord
{
    protected static string $resource = ModerationActionResource::class;
}
