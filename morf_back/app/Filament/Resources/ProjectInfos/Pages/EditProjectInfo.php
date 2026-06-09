<?php

namespace App\Filament\Resources\ProjectInfos\Pages;

use App\Filament\Resources\ProjectInfos\ProjectInfoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectInfo extends EditRecord
{
    protected static string $resource = ProjectInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
