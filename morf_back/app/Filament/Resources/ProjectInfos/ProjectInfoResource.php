<?php

namespace App\Filament\Resources\ProjectInfos;

use App\Contexts\Static\Domain\ProjectInfo;
use App\Filament\Resources\ProjectInfos\Pages\CreateProjectInfo;
use App\Filament\Resources\ProjectInfos\Pages\EditProjectInfo;
use App\Filament\Resources\ProjectInfos\Pages\ListProjectInfos;
use App\Filament\Resources\ProjectInfos\Schemas\ProjectInfoForm;
use App\Filament\Resources\ProjectInfos\Tables\ProjectInfosTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectInfoResource extends Resource
{
    protected static ?string $model = ProjectInfo::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Static';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProjectInfoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectInfosTable::configure($table);
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
            'index' => ListProjectInfos::route('/'),
            'create' => CreateProjectInfo::route('/create'),
            'edit' => EditProjectInfo::route('/{record}/edit'),
        ];
    }
}
