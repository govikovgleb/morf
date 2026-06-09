<?php

namespace App\Filament\Resources\ReferenceSets\Schemas;

use App\Contexts\Identity\Domain\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReferenceSetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('week_start_date')
                    ->required(),
                Toggle::make('is_published')
                    ->required()
                    ->default(false),
                DateTimePicker::make('published_at'),
                Select::make('created_by')
                    ->label('Created By')
                    ->options(fn () => User::pluck('public_nickname', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
