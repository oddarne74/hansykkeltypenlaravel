<?php

namespace App\Http\Controllers;

use App\Enums\BikeStatus;
use App\Models\Bike;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Cache::remember(Bike::FEATURED_BIKES_CACHE_KEY, now()->addHour(), function () {
            $featuredBikes = Bike::published()
                ->with('images')
                ->where('featured', true)
                ->where('status', BikeStatus::FOR_SALE)
                ->inRandomOrder()
                ->limit(5)
                ->get();

            if ($featuredBikes->isEmpty()) {
                $featuredBikes = Bike::published()
                    ->with('images')
                    ->where('featured', true)
                    ->where('status', BikeStatus::IN_PROGRESS)
                    ->inRandomOrder()
                    ->limit(5)
                    ->get();
            }

            if ($featuredBikes->isEmpty()) {
                $featuredBikes = Bike::published()
                    ->with('images')
                    ->where('status', BikeStatus::IN_PROGRESS)
                    ->inRandomOrder()
                    ->limit(5)
                    ->get();
            }

            return $featuredBikes;
        });

        return view('home', ['featured' => $featured]);
    }
}
