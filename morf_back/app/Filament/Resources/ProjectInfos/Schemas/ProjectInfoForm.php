<?php

namespace App\Filament\Resources\ProjectInfos\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectInfoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('value')
                    ->required()
                    ->helperText('JSON string or plain text')
                    ->rows(5),
            ]);
    }
}
