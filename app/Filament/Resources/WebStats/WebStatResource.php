<?php

namespace App\Filament\Resources\WebStats;

use App\Filament\Resources\WebStats\Pages\CreateWebStat;
use App\Filament\Resources\WebStats\Pages\EditWebStat;
use App\Filament\Resources\WebStats\Pages\ListWebStats;
use App\Filament\Resources\WebStats\Pages\ViewWebStat;
use App\Filament\Resources\WebStats\Schemas\WebStatForm;
use App\Filament\Resources\WebStats\Schemas\WebStatInfolist;
use App\Filament\Resources\WebStats\Tables\WebStatsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use WebStats\Models\WebStat;

class WebStatResource extends Resource
{
    protected static ?string $model = WebStat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $modelLabel = 'nettsidestatistikk';

    protected static ?string $pluralModelLabel = 'nettsidestatistikk';

    protected static ?string $navigationLabel = 'Statistikk';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return WebStatForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WebStatInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebStatsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebStats::route('/'),
            'create' => CreateWebStat::route('/create'),
            'view' => ViewWebStat::route('/{record}'),
            'edit' => EditWebStat::route('/{record}/edit'),
        ];
    }
}
