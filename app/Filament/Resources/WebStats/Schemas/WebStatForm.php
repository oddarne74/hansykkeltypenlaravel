<?php

namespace App\Filament\Resources\WebStats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WebStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Statistikkdetaljer')
                    ->schema([
                        TextInput::make('title')
                            ->label('Sidetittel')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(500),
                    ]),
            ]);
    }
}
