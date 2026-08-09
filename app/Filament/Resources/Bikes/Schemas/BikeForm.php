<?php

namespace App\Filament\Resources\Bikes\Schemas;

use App\Enums\BikeSize;
use App\Enums\BikeType;
use App\Models\Bike;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BikeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grunnleggende')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Navn')
                            ->placeholder('F.eks. Trek 7200 hybridsykkel')
                            ->required()
                            ->maxLength(120),
                        Select::make('type')
                            ->label('Type')
                            ->options(BikeType::options())
                            ->searchable()
                            ->required(),
                        TextInput::make('brand')
                            ->label('Merke')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('model')
                            ->label('Modell')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('price')
                            ->label('Pris')
                            ->suffix('kr')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000000),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Til salgs' => 'Til salgs',
                                'Reservert' => 'Reservert',
                                'Solgt' => 'Solgt',
                            ])
                            ->default('Til salgs')
                            ->required(),
                    ]),
                Section::make('Spesifikasjoner')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('size')
                            ->label('Størrelse')
                            ->options(BikeSize::options())
                            ->searchable()
                            ->required(),
                        TextInput::make('rider_height')
                            ->label('Passer høyde')
                            ->placeholder('F.eks. 165–180 cm')
                            ->maxLength(100),
                        TextInput::make('wheel_size')
                            ->label('Hjulstørrelse')
                            ->placeholder('F.eks. 28"')
                            ->maxLength(100),
                        TextInput::make('gears')
                            ->label('Gir')
                            ->placeholder('F.eks. 3x8 Shimano')
                            ->maxLength(100),
                        TextInput::make('frame')
                            ->label('Ramme')
                            ->placeholder('F.eks. Aluminium')
                            ->maxLength(100),
                        TextInput::make('brakes')
                            ->label('Bremser')
                            ->placeholder('F.eks. V-brems')
                            ->maxLength(100),
                        TextInput::make('color')
                            ->label('Farge')
                            ->maxLength(100),
                        TextInput::make('year')
                            ->label('Årsmodell')
                            ->placeholder('F.eks. 2018')
                            ->maxLength(100),
                    ]),
                Section::make('Beskrivelse')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('description')
                            ->label('Beskrivelse')
                            ->rows(6)
                            ->required()
                            ->maxLength(5000),
                        Textarea::make('condition_notes')
                            ->label('Tilstand')
                            ->rows(4)
                            ->maxLength(5000),
                    ]),
                Section::make('Utført arbeid')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('workItems')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->reorderable()
                            ->addActionLabel('Legg til punkt')
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->components([
                                TextInput::make('title')
                                    ->label('Tittel')
                                    ->placeholder('F.eks. Gir og bremser')
                                    ->required()
                                    ->maxLength(190),
                                Textarea::make('description')
                                    ->label('Beskrivelse')
                                    ->placeholder('F.eks. Kontrollert og justert.')
                                    ->rows(2)
                                    ->maxLength(1000),
                            ]),
                    ]),
                Section::make('Synlighet')
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('published')
                            ->label('Publisert (synlig på nettstedet)')
                            ->afterStateHydrated(function (Toggle $component, ?Bike $record): void {
                                $component->state($record?->published_at !== null);
                            }),
                        Toggle::make('featured')
                            ->label('Fremhevet på forsiden'),
                    ]),
            ]);
    }
}
