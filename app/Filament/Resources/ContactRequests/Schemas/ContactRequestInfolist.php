<?php

namespace App\Filament\Resources\ContactRequests\Schemas;

use App\Models\ContactRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Henvendelse')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('name')
                            ->label('Navn'),
                        TextEntry::make('contact')
                            ->label('E-post eller telefon')
                            ->copyable(),
                        TextEntry::make('subject')
                            ->label('Gjelder')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ContactRequest::SUBJECTS[$state] ?? $state),
                        TextEntry::make('created_at')
                            ->label('Mottatt')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('message')
                            ->label('Melding')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
