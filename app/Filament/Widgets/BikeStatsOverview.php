<?php

namespace App\Filament\Widgets;

use App\Models\Bike;
use App\Models\ContactRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BikeStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Sykler totalt', Bike::count()),
            Stat::make('Publisert', Bike::published()->count()),
            Stat::make('Til salgs', Bike::where('status', 'Til salgs')->count()),
            Stat::make('Nye henvendelser', ContactRequest::whereNull('read_at')->count())
                ->description(ContactRequest::count().' totalt'),
        ];
    }
}
