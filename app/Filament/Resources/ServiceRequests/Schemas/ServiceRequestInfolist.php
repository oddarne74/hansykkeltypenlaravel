<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\Service;
use App\Enums\ServiceStatus;
use App\Models\ServiceRequest;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ServiceRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kunde & Bestilling')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('name')
                            ->label('Navn'),
                        TextEntry::make('email')
                            ->label('E-post')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Telefon')
                            ->placeholder('Ikke oppgitt')
                            ->copyable(),
                        TextEntry::make('service_type')
                            ->label('Valgt service')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (Service|string|null $state): string => $state instanceof Service ? $state->labelWithPrice() : (string) $state),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (ServiceStatus|string|null $state): string => $state instanceof ServiceStatus ? $state->color() : 'gray')
                            ->formatStateUsing(fn (ServiceStatus|string|null $state): string => $state instanceof ServiceStatus ? $state->value : (string) $state),
                        TextEntry::make('week_start')
                            ->label('Ønsket uke')
                            ->formatStateUsing(fn (ServiceRequest $record): string => $record->week_start ? sprintf('Uke %d (%s)', Carbon::parse($record->week_start)->isoWeek(), Carbon::parse($record->week_start)->format('d.m.Y')) : '-'),
                        TextEntry::make('wants_pickup')
                            ->label('Henting og levering')
                            ->badge()
                            ->state(fn (ServiceRequest $record): string => $record->wants_pickup ? 'Ja' : 'Nei')
                            ->color(fn (string $state): string => $state === 'Ja' ? 'info' : 'gray'),
                        TextEntry::make('address')
                            ->label('Adresse for henting')
                            ->placeholder('Ikke oppgitt')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Mottatt')
                            ->dateTime('d.m.Y H:i'),
                    ]),
                Section::make('Beskrivelse / kommentarer')
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('message')
                            ->label('Melding fra kunden')
                            ->columnSpanFull(),
                    ]),
                Section::make('Bilder av sykkelen')
                    ->columnSpanFull()
                    ->components([
                        ImageEntry::make('images')
                            ->label('')
                            ->disk('public')
                            ->placeholder('Ingen bilder lastet opp')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
