<?php
namespace App\Http\Controllers;
use App\Models\Bike;
class BikeController extends Controller
{
    public function index(){ return view('bikes.index',['bikes'=>Bike::published()->with('images')->latest('published_at')->paginate(12)]); }
    public function show(Bike $bike){ abort_unless($bike->published_at?->isPast(),404); return view('bikes.show',['bike'=>$bike->load(['images','workItems'])]); }
}
