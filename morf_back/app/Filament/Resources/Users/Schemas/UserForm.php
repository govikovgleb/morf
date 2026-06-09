<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('public_nickname')
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ])
                    ->required()
                    ->default('user'),
                TextInput::make('auth_hash')
                    ->required()
                    ->maxLength(255)
                    ->label('Auth Hash'),
                TextInput::make('recovery_code_hash')
                    ->maxLength(255)
                    ->label('Recovery Code Hash'),
                TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }
}
