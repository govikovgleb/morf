<?php

namespace App\Filament\Resources\ReferenceSets\Pages;

use App\Filament\Resources\ReferenceSets\ReferenceSetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferenceSets extends ListRecords
{
    protected static string $resource = ReferenceSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
