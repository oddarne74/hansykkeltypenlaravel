<?php

namespace App\Filament\Widgets;

use App\Enums\BikeStatus;
use App\Enums\ServiceStatus;
use App\Models\Bike;
use App\Models\ContactRequest;
use App\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BikeStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Sykler totalt', Bike::count()),
            Stat::make('Publisert', Bike::published()->count()),
            Stat::make('Til salgs', Bike::where('status', BikeStatus::FOR_SALE)->count()),
            Stat::make('Nye serviceforespørsler', ServiceRequest::where('status', ServiceStatus::PENDING)->count())
                ->description(ServiceRequest::count().' totalt'),
            Stat::make('Nye henvendelser', ContactRequest::whereNull('read_at')->count())
                ->description(ContactRequest::count().' totalt'),
        ];
    }
}
