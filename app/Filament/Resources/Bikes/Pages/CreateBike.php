<?php

namespace App\Filament\Resources\Bikes\Pages;

use App\Filament\Resources\Bikes\BikeResource;
use App\Models\Bike;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBike extends CreateRecord
{
    protected static string $resource = BikeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['published_at'] = ($data['published'] ?? false) ? now() : null;
        unset($data['published']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'sykkel';
        $slug = $base;

        for ($i = 2; Bike::where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
