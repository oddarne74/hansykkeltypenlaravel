<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $bikes = Bike::published()->latest('updated_at')->get();

        return response()->view('sitemap', [
            'bikes' => $bikes,
        ])->header('Content-Type', 'text/xml');
    }
}
