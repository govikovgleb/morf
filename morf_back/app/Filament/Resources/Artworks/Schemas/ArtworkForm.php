<?php

namespace App\Filament\Resources\Artworks\Schemas;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArtworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Author')
                    ->options(fn () => User::pluck('public_nickname', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('reference_set_id')
                    ->label('Reference Set')
                    ->options(fn () => ReferenceSet::pluck('title', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
                Textarea::make('caption')
                    ->maxLength(65535),
                TextInput::make('cdn_url')
                    ->maxLength(2048)
                    ->url()
                    ->suffixAction(
                        Action::make('view')
                            ->icon('heroicon-m-eye')
                            ->url(fn ($record) => $record?->cdn_url ?? '#')
                            ->openUrlInNewTab()
                    ),
                TextInput::make('width')
                    ->numeric(),
                TextInput::make('height')
                    ->numeric(),
                TextInput::make('file_size_bytes')
                    ->numeric(),
                TextInput::make('mime_type')
                    ->maxLength(255),
                TextInput::make('likes_count')
                    ->numeric()
                    ->default(0),
                TextInput::make('author_nickname')
                    ->maxLength(255),
                Select::make('moderated_by')
                    ->label('Moderated By')
                    ->options(fn () => User::pluck('public_nickname', 'id'))
                    ->searchable(),
                DateTimePicker::make('moderated_at'),
                Textarea::make('moderated_reason')
                    ->maxLength(65535),
            ]);
    }
}
