<?php

use App\Http\Controllers\BikeController;
use App\Http\Controllers\BikeInterestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sykler', [BikeController::class, 'index'])->name('bikes.index');
Route::get('/sykler/{bike:slug}', [BikeController::class, 'show'])->name('bikes.show');
Route::post('/sykler/{bike:slug}/interessert', [BikeInterestController::class, 'store'])->name('bikes.interest.store');
Route::post('/kontakt', [ContactController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');

Route::get('/bodo', [LocationController::class, 'bodo'])->name('locations.bodo');
Route::get('/fauske', [LocationController::class, 'fauske'])->name('locations.fauske');
Route::get('/rognan', [LocationController::class, 'rognan'])->name('locations.rognan');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// The admin panel is powered by Filament and lives at /admin.
Route::redirect('/dashboard', '/admin')->name('dashboard');

require __DIR__.'/settings.php';
