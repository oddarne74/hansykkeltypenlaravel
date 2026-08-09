<?php

namespace App\Http\Controllers;

use App\Enums\BikeStatus;
use App\Models\Bike;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Bike::published()
            ->with('images')
            ->where('featured', true)
            ->where('status', BikeStatus::FOR_SALE)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('home', ['featured' => $featured]);
    }
}
