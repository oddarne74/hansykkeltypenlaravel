<?php

namespace App\Filament\Resources\Bikes\Pages;

use App\Filament\Resources\Bikes\BikeResource;
use App\Models\Bike;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBike extends EditRecord
{
    protected static string $resource = BikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_on_site')
                ->label('Vis på nettstedet')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => route('bikes.show', $this->getRecord()))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()->getAttribute('published_at') !== null),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Bike $bike */
        $bike = $this->getRecord();

        // Keep the original publish date when the bike is already published.
        $data['published_at'] = ($data['published'] ?? false) ? ($bike->published_at ?? now()) : null;
        unset($data['published']);

        return $data;
    }
}
