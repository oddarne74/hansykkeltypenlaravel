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
            return Bike::published()
                ->with('images')
                ->where('featured', true)
                ->where('status', BikeStatus::FOR_SALE)
                ->inRandomOrder()
                ->limit(5)
                ->get();
        });

        return view('home', ['featured' => $featured]);
    }
}
