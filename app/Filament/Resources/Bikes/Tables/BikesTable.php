<?php

namespace App\Filament\Resources\Bikes\Tables;

use App\Enums\BikeSize;
use App\Enums\BikeStatus;
use App\Enums\BikeType;
use App\Models\Bike;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BikesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Sykkel')
                    ->description(fn (Bike $record): string => trim($record->brand.' '.$record->model))
                    ->searchable(['name', 'brand', 'model'])
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Størrelse')
                    ->badge()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Pris')
                    ->formatStateUsing(fn (int $state): string => number_format($state, 0, ',', ' ').' kr')
                    ->sortable(),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options(BikeStatus::options())
                    ->selectablePlaceholder(false)
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Synlighet')
                    ->badge()
                    ->formatStateUsing(fn (): string => 'Publisert')
                    ->placeholder('Kladd')
                    ->color('success')
                    ->sortable(),
                IconColumn::make('featured')
                    ->label('Fremhevet')
                    ->boolean(),
                TextColumn::make('images_count')
                    ->label('Bilder')
                    ->counts('images'),
                TextColumn::make('created_at')
                    ->label('Opprettet')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(BikeType::options()),
                SelectFilter::make('size')
                    ->label('Størrelse')
                    ->options(BikeSize::options()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BikeStatus::options()),
                TernaryFilter::make('featured')
                    ->label('Fremhevet')
                    ->boolean(),
                TernaryFilter::make('published_at')
                    ->label('Synlighet')
                    ->nullable()
                    ->trueLabel('Publisert')
                    ->falseLabel('Kladd')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('published_at'),
                        false: fn ($query) => $query->whereNull('published_at'),
                    ),
            ])
            ->recordActions([
                Action::make('toggle_published')
                    ->label(fn (Bike $record): string => $record->published_at === null ? 'Publiser' : 'Avpubliser')
                    ->icon(fn (Bike $record): string => $record->published_at === null ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn (Bike $record): string => $record->published_at === null ? 'success' : 'gray')
                    ->action(function (Bike $record, Action $action): void {
                        $record->update(['published_at' => $record->published_at === null ? now() : null]);

                        $action->successNotificationTitle($record->published_at !== null ? 'Sykkelen er publisert' : 'Sykkelen er avpublisert');
                        $action->success();
                    }),
                Action::make('view_on_site')
                    ->label('Vis')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Bike $record): string => route('bikes.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Bike $record): bool => $record->published_at !== null),
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
