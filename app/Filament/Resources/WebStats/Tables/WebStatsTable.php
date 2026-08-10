<?php

namespace App\Filament\Resources\WebStats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use WebStats\Models\WebStat;

class WebStatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Sidetittel')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('views_today')
                    ->label('I dag')
                    ->state(fn (WebStat $record): int => $record->views_today)
                    ->sortable(),
                TextColumn::make('views_this_week')
                    ->label('Denne uken')
                    ->state(fn (WebStat $record): int => $record->views_this_week)
                    ->sortable(),
                TextColumn::make('views_this_month')
                    ->label('Denne måneden')
                    ->state(fn (WebStat $record): int => $record->views_this_month)
                    ->sortable(),
                TextColumn::make('total_views')
                    ->label('Totalt')
                    ->state(fn (WebStat $record): int => $record->total_views)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Opprettet')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
