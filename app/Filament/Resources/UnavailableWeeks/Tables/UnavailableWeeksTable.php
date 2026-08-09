<?php

namespace App\Filament\Resources\UnavailableWeeks\Tables;

use App\Models\UnavailableWeek;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class UnavailableWeeksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('week_start', 'asc')
            ->columns([
                TextColumn::make('week_start')
                    ->label('Uke')
                    ->state(fn (UnavailableWeek $record): string => $record->week_start ? sprintf('Uke %d (%s–%s)', Carbon::parse($record->week_start)->isoWeek(), Carbon::parse($record->week_start)->format('d.m.Y'), Carbon::parse($record->week_start)->endOfWeek()->format('d.m.Y')) : '-')
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Årsak / Kommentar')
                    ->placeholder('Ingen oppgitt')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Lagt til')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
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
