<?php

namespace App\Filament\Resources\Modifications;

use App\Filament\Resources\Modifications\Pages\CreateModification;
use App\Filament\Resources\Modifications\Pages\EditModification;
use App\Filament\Resources\Modifications\Pages\ListModifications;
use App\Filament\Resources\Modifications\Schemas\ModificationForm;
use App\Filament\Resources\Modifications\Tables\ModificationsTable;
use App\Models\Modification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ModificationResource extends Resource
{
    protected static ?string $model = Modification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ModificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModificationsTable::configure($table);
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
            'index' => ListModifications::route('/'),
            'create' => CreateModification::route('/create'),
            'edit' => EditModification::route('/{record}/edit'),
        ];
    }
}
