<?php

namespace App\Filament\Resources\ModerationActions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModerationActionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target_type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target_id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->sortable(),
                TextColumn::make('actor.public_nickname')
                    ->label('Actor')
                    ->sortable(),
                TextColumn::make('reason')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'approve' => 'Approve',
                        'reject' => 'Reject',
                    ]),
                SelectFilter::make('target_type')
                    ->options([
                        'artwork' => 'Artwork',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
