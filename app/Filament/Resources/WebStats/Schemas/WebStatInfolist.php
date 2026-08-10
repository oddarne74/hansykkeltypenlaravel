<?php

namespace App\Filament\Resources\WebStats\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use WebStats\Models\WebStat;

class WebStatInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sideinformasjon')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('title')
                                ->label('Sidetittel'),
                            TextEntry::make('url')
                                ->label('URL')
                                ->url(fn (WebStat $record): string => $record->url, true),
                        ]),
                    ]),
                Section::make('Visningsstatistikk (Dag, Uke, Måned)')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('views_today')
                                ->label('Visninger i dag')
                                ->state(fn (WebStat $record): int => $record->views_today),
                            TextEntry::make('views_this_week')
                                ->label('Visninger denne uken')
                                ->state(fn (WebStat $record): int => $record->views_this_week),
                            TextEntry::make('views_this_month')
                                ->label('Visninger denne måneden')
                                ->state(fn (WebStat $record): int => $record->views_this_month),
                            TextEntry::make('total_views')
                                ->label('Totalt visninger')
                                ->state(fn (WebStat $record): int => $record->total_views),
                        ]),
                    ]),
            ]);
    }
}
