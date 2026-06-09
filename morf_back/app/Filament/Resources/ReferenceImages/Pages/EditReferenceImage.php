<?php

namespace App\Filament\Resources\ReferenceImages\Pages;

use App\Filament\Resources\ReferenceImages\ReferenceImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferenceImage extends EditRecord
{
    protected static string $resource = ReferenceImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
