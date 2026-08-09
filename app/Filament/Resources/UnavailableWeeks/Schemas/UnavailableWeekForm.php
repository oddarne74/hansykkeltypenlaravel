<?php

namespace App\Filament\Resources\UnavailableWeeks\Schemas;

use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnavailableWeekForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ukedetaljer')
                    ->components([
                        DatePicker::make('week_start')
                            ->label('Uke / dato')
                            ->helperText('Velg en dato i uken som skal sperres. Datoen lagres automatisk som mandagen i den uken.')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? CarbonImmutable::parse($state)->startOfWeek()->format('Y-m-d') : null),
                        TextInput::make('reason')
                            ->label('Årsak / Kommentar')
                            ->placeholder('F.eks. Ferie, verksted stengt, kurs')
                            ->maxLength(190),
                    ]),
            ]);
    }
}
