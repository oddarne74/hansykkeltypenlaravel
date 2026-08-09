<?php

namespace App\Filament\Resources\ContactRequests\Tables;

use App\Models\ContactRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->state(fn (ContactRequest $record): string => $record->read_at === null ? 'Ny' : 'Lest')
                    ->color(fn (string $state): string => $state === 'Ny' ? 'warning' : 'gray'),
                TextColumn::make('name')
                    ->label('Navn')
                    ->description(fn (ContactRequest $record): string => $record->contact)
                    ->searchable(['name', 'contact'])
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Gjelder')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactRequest::SUBJECTS[$state] ?? $state),
                TextColumn::make('message')
                    ->label('Melding')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Mottatt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->nullable()
                    ->trueLabel('Lest')
                    ->falseLabel('Ny')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                    ),
                SelectFilter::make('subject')
                    ->label('Gjelder')
                    ->options(ContactRequest::SUBJECTS),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
