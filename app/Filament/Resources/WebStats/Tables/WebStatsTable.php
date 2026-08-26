<?php

namespace App\Filament\Resources\WebStats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use WebStats\Models\WebStat;

class WebStatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('views_today', 'desc')
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
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withCount([
                                'hits as views_today_count' => fn (Builder $q) => $q->whereDate('created_at', today()),
                            ])
                            ->orderBy('views_today_count', $direction);
                    }),
                TextColumn::make('views_this_week')
                    ->label('Denne uken')
                    ->state(fn (WebStat $record): int => $record->views_this_week)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withCount([
                                'hits as views_this_week_count' => fn (Builder $q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                            ])
                            ->orderBy('views_this_week_count', $direction);
                    }),
                TextColumn::make('views_this_month')
                    ->label('Denne måneden')
                    ->state(fn (WebStat $record): int => $record->views_this_month)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withCount([
                                'hits as views_this_month_count' => fn (Builder $q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                            ])
                            ->orderBy('views_this_month_count', $direction);
                    }),
                TextColumn::make('total_views')
                    ->label('Totalt')
                    ->state(fn (WebStat $record): int => $record->total_views)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->withCount('hits')
                            ->orderBy('hits_count', $direction);
                    }),
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
