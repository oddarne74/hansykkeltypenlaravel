<?php

namespace App\Filament\Resources\UnavailableWeeks\Pages;

use App\Filament\Resources\UnavailableWeeks\UnavailableWeekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnavailableWeeks extends ListRecords
{
    protected static string $resource = UnavailableWeekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
