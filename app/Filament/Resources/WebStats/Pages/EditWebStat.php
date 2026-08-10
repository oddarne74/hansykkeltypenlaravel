<?php

namespace App\Filament\Resources\WebStats\Pages;

use App\Filament\Resources\WebStats\WebStatResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWebStat extends EditRecord
{
    protected static string $resource = WebStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
