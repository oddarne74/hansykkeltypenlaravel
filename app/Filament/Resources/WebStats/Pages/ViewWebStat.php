<?php

namespace App\Filament\Resources\WebStats\Pages;

use App\Filament\Resources\WebStats\WebStatResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWebStat extends ViewRecord
{
    protected static string $resource = WebStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
