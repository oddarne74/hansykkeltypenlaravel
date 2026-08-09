<?php
use App\Http\Controllers\BikeController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
Route::view('/', 'home')->name('home');
Route::get('/sykler', [BikeController::class, 'index'])->name('bikes.index');
Route::get('/sykler/{bike:slug}', [BikeController::class, 'show'])->name('bikes.show');
Route::post('/kontakt', [ContactController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');
