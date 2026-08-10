<?php

namespace App\Filament\Resources\WebStats\Pages;

use App\Filament\Resources\WebStats\WebStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebStats extends ListRecords
{
    protected static string $resource = WebStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
