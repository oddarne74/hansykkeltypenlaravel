<?php

namespace Database\Seeders;

use App\Models\UnavailableWeek;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class UnavailableWeekSeeder extends Seeder
{
    public function run(): void
    {
        $weeks = [
            [
                'week_start' => CarbonImmutable::now()->startOfWeek()->addWeeks(2)->format('Y-m-d'),
                'reason' => 'Ferie',
            ],
            [
                'week_start' => CarbonImmutable::now()->startOfWeek()->addWeeks(6)->format('Y-m-d'),
                'reason' => 'Verksted stengt',
            ],
            [
                'week_start' => CarbonImmutable::now()->startOfWeek()->addWeeks(10)->format('Y-m-d'),
                'reason' => 'Kurs og vedlikehold',
            ],
        ];

        foreach ($weeks as $week) {
            UnavailableWeek::updateOrCreate(
                ['week_start' => $week['week_start']],
                ['reason' => $week['reason']],
            );
        }
    }
}
