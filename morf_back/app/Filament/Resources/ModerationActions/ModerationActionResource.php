<?php

namespace App\Filament\Resources\ModerationActions;

use App\Contexts\Moderation\Domain\ModerationAction;
use App\Filament\Resources\ModerationActions\Pages\CreateModerationAction;
use App\Filament\Resources\ModerationActions\Pages\EditModerationAction;
use App\Filament\Resources\ModerationActions\Pages\ListModerationActions;
use App\Filament\Resources\ModerationActions\Schemas\ModerationActionForm;
use App\Filament\Resources\ModerationActions\Tables\ModerationActionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ModerationActionResource extends Resource
{
    protected static ?string $model = ModerationAction::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Moderation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ModerationActionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModerationActionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModerationActions::route('/'),
            'create' => CreateModerationAction::route('/create'),
            'edit' => EditModerationAction::route('/{record}/edit'),
        ];
    }
}
