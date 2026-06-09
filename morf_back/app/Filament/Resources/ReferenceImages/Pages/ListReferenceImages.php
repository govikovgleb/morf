<?php

namespace App\Filament\Resources\ReferenceImages\Pages;

use App\Filament\Resources\ReferenceImages\ReferenceImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferenceImages extends ListRecords
{
    protected static string $resource = ReferenceImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
