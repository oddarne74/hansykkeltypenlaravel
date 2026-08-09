<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
