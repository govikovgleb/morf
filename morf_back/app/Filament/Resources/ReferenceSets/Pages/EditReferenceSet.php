<?php

namespace App\Filament\Resources\ReferenceSets\Pages;

use App\Filament\Resources\ReferenceSets\ReferenceSetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferenceSet extends EditRecord
{
    protected static string $resource = ReferenceSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
