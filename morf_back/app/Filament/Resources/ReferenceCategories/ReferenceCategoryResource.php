<?php

namespace App\Filament\Resources\ReferenceCategories;

use App\Contexts\Content\Domain\ReferenceCategory;
use App\Filament\Resources\ReferenceCategories\Pages\CreateReferenceCategory;
use App\Filament\Resources\ReferenceCategories\Pages\EditReferenceCategory;
use App\Filament\Resources\ReferenceCategories\Pages\ListReferenceCategories;
use App\Filament\Resources\ReferenceCategories\Schemas\ReferenceCategoryForm;
use App\Filament\Resources\ReferenceCategories\Tables\ReferenceCategoriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReferenceCategoryResource extends Resource
{
    protected static ?string $model = ReferenceCategory::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReferenceCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferenceCategoriesTable::configure($table);
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
            'index' => ListReferenceCategories::route('/'),
            'create' => CreateReferenceCategory::route('/create'),
            'edit' => EditReferenceCategory::route('/{record}/edit'),
        ];
    }
}
