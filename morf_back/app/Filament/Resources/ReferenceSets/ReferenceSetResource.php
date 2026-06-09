<?php

namespace App\Filament\Resources\ReferenceSets;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Filament\Resources\ReferenceSets\Pages\CreateReferenceSet;
use App\Filament\Resources\ReferenceSets\Pages\EditReferenceSet;
use App\Filament\Resources\ReferenceSets\Pages\ListReferenceSets;
use App\Filament\Resources\ReferenceSets\Schemas\ReferenceSetForm;
use App\Filament\Resources\ReferenceSets\Tables\ReferenceSetsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReferenceSetResource extends Resource
{
    protected static ?string $model = ReferenceSet::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReferenceSetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferenceSetsTable::configure($table);
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
            'index' => ListReferenceSets::route('/'),
            'create' => CreateReferenceSet::route('/create'),
            'edit' => EditReferenceSet::route('/{record}/edit'),
        ];
    }
}
