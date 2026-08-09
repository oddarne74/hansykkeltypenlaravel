<?php

namespace App\Http\Controllers;

use App\Enums\BikeStatus;
use App\Models\Bike;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => 'nullable|string|max:50',
            'brand' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:20',
            'gears' => 'nullable|string|max:100',
            'price_min' => 'nullable|integer|min:0',
            'price_max' => 'nullable|integer|min:0',
        ]);

        $bikes = Bike::published()
            ->with('images')
            ->whereIn('status', [BikeStatus::FOR_SALE->value, BikeStatus::RESERVED->value])
            ->when($filters['brand'] ?? null, fn ($query, $brand) => $query->where('brand', $brand))
            ->when($filters['size'] ?? null, fn ($query, $size) => $query->where('size', $size))
            ->when($filters['gears'] ?? null, fn ($query, $gears) => $query->where('gears', $gears))
            ->when($filters['price_min'] ?? null, fn ($query, $priceMin) => $query->where('price', '>=', (int) $priceMin))
            ->when($filters['price_max'] ?? null, fn ($query, $priceMax) => $query->where('price', '<=', (int) $priceMax))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('bikes.index', [
            'bikes' => $bikes,
            'filters' => $filters,
            'filterOptions' => [
                'brands' => Bike::published()->distinct()->orderBy('brand')->pluck('brand'),
                'sizes' => Bike::published()->whereNotNull('size')->distinct()->orderBy('size')->pluck('size'),
                'gears' => Bike::published()->whereNotNull('gears')->distinct()->orderBy('gears')->pluck('gears'),
            ],
        ]);
    }

    public function show(Bike $bike)
    {
        abort_unless($bike->published_at?->isPast(), 404);

        return view('bikes.show', ['bike' => $bike->load(['images', 'workItems'])]);
    }
}
