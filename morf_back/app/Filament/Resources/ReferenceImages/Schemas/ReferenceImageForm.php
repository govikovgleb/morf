<?php

namespace App\Filament\Resources\ReferenceImages\Schemas;

use App\Contexts\Content\Domain\ReferenceCategory;
use App\Contexts\Identity\Domain\User;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReferenceImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => ReferenceCategory::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('cdn_url')
                    ->required()
                    ->maxLength(2048)
                    ->url()
                    ->suffixAction(
                        Action::make('view')
                            ->icon('heroicon-m-eye')
                            ->url(fn ($record) => $record?->cdn_url ?? '#')
                            ->openUrlInNewTab()
                    ),
                TextInput::make('storage_path')
                    ->maxLength(2048),
                TextInput::make('width')
                    ->numeric()
                    ->required(),
                TextInput::make('height')
                    ->numeric()
                    ->required(),
                TextInput::make('file_size_bytes')
                    ->numeric()
                    ->required(),
                TextInput::make('mime_type')
                    ->required()
                    ->maxLength(255),
                Select::make('uploaded_by')
                    ->label('Uploaded By')
                    ->options(fn () => User::pluck('public_nickname', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
