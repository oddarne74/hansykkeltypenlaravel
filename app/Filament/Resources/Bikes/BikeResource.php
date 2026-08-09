<?php

namespace App\Filament\Resources\Bikes;

use App\Filament\Resources\Bikes\Pages\CreateBike;
use App\Filament\Resources\Bikes\Pages\EditBike;
use App\Filament\Resources\Bikes\Pages\ListBikes;
use App\Filament\Resources\Bikes\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Bikes\Schemas\BikeForm;
use App\Filament\Resources\Bikes\Tables\BikesTable;
use App\Models\Bike;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BikeResource extends Resource
{
    protected static ?string $model = Bike::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $modelLabel = 'sykkel';

    protected static ?string $pluralModelLabel = 'sykler';

    protected static ?string $navigationLabel = 'Sykler';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BikeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BikesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBikes::route('/'),
            'create' => CreateBike::route('/create'),
            'edit' => EditBike::route('/{record}/edit'),
        ];
    }
}
