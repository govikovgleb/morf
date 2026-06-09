<?php

namespace App\Filament\Resources\ModerationActions\Pages;

use App\Filament\Resources\ModerationActions\ModerationActionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModerationAction extends EditRecord
{
    protected static string $resource = ModerationActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
