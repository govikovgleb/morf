<?php

namespace App\Filament\Resources\ReferenceCategories\Pages;

use App\Filament\Resources\ReferenceCategories\ReferenceCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferenceCategory extends EditRecord
{
    protected static string $resource = ReferenceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
