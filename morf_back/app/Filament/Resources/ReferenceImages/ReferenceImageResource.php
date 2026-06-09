<?php

namespace App\Filament\Resources\ReferenceImages;

use App\Contexts\Content\Domain\ReferenceImage;
use App\Filament\Resources\ReferenceImages\Pages\CreateReferenceImage;
use App\Filament\Resources\ReferenceImages\Pages\EditReferenceImage;
use App\Filament\Resources\ReferenceImages\Pages\ListReferenceImages;
use App\Filament\Resources\ReferenceImages\Schemas\ReferenceImageForm;
use App\Filament\Resources\ReferenceImages\Tables\ReferenceImagesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReferenceImageResource extends Resource
{
    protected static ?string $model = ReferenceImage::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReferenceImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferenceImagesTable::configure($table);
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
            'index' => ListReferenceImages::route('/'),
            'create' => CreateReferenceImage::route('/create'),
            'edit' => EditReferenceImage::route('/{record}/edit'),
        ];
    }
}
