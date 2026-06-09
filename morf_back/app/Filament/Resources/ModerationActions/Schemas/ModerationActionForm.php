<?php

namespace App\Filament\Resources\ModerationActions\Schemas;

use App\Contexts\Identity\Domain\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModerationActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('target_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('target_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Select::make('actor_id')
                    ->label('Actor')
                    ->options(fn () => User::pluck('public_nickname', 'id'))
                    ->required()
                    ->searchable(),
                Textarea::make('reason')
                    ->maxLength(65535),
            ]);
    }
}
