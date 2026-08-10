<?php

namespace App\Http\Controllers;

use App\Enums\Service;
use Illuminate\Contracts\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        return view('pricing', [
            'services' => Service::cases(),
        ]);
    }
}
