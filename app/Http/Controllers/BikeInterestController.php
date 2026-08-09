<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Illuminate\Http\Request;

class BikeInterestController extends Controller
{
    public function store(Request $request, Bike $bike)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $bike->interests()->create([
            'email' => $request->email,
        ]);

        return back()->with('success', 'Vi har registrert din interesse! Vi gir deg beskjed hvis sykkelen blir ledig.');
    }
}
