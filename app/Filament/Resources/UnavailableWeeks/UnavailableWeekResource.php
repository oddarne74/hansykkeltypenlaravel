<?php

namespace App\Filament\Resources\UnavailableWeeks;

use App\Filament\Resources\UnavailableWeeks\Pages\ListUnavailableWeeks;
use App\Filament\Resources\UnavailableWeeks\Schemas\UnavailableWeekForm;
use App\Filament\Resources\UnavailableWeeks\Tables\UnavailableWeeksTable;
use App\Models\UnavailableWeek;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnavailableWeekResource extends Resource
{
    protected static ?string $model = UnavailableWeek::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'utilgjengelig uke';

    protected static ?string $pluralModelLabel = 'utilgjengelige uker';

    protected static ?string $navigationLabel = 'Utilgjengelige uker';

    public static function form(Schema $schema): Schema
    {
        return UnavailableWeekForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnavailableWeeksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnavailableWeeks::route('/'),
        ];
    }
}
