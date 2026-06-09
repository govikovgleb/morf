<?php

namespace App\Filament\Resources\ProjectInfos\Pages;

use App\Filament\Resources\ProjectInfos\ProjectInfoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectInfos extends ListRecords
{
    protected static string $resource = ProjectInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
