<?php

namespace App\Filament\Resources\ReferenceCategories\Pages;

use App\Filament\Resources\ReferenceCategories\ReferenceCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferenceCategories extends ListRecords
{
    protected static string $resource = ReferenceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
