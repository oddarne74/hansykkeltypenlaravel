<?php

namespace App\Http\Controllers;

class LocationController extends Controller
{
    public function bodo()
    {
        return view('locations.bodo');
    }

    public function fauske()
    {
        return view('locations.fauske');
    }

    public function rognan()
    {
        return view('locations.rognan');
    }
}
