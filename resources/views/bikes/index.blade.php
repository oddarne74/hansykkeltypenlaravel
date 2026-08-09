@extends('layouts.site')

@section('title', 'Sykler til salgs | Han Sykkeltypen')

@section('content')
<section class="bg-forest px-5 py-16 text-white lg:px-8">
    <div class="mx-auto max-w-7xl">
        <p class="eyebrow text-sun">Klargjort i Salten</p>
        <h1 class="font-display text-5xl uppercase sm:text-7xl">Sykler til salgs</h1>
        <p class="mt-5 max-w-2xl text-lg text-white/70">Kontrollert, rengjort, justert og prøvekjørt. Hver side viser størrelse, tilstand og nøyaktig hva som er gjort.</p>
    </div>
</section>

<section class="grain min-h-[50vh] px-5 py-16 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <form method="get" action="{{ route('bikes.index') }}" class="card mb-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <label>Merke
                <select name="brand">
                    <option value="">Alle</option>
                    @foreach($filterOptions['brands'] as $brand)
                        <option value="{{ $brand }}" @selected(($filters['brand'] ?? null) === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </label>
            <label>Størrelse
                <select name="size">
                    <option value="">Alle</option>
                    @foreach($filterOptions['sizes'] as $size)
                        <option value="{{ $size }}" @selected(($filters['size'] ?? null) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </label>
            <label>Gir
                <select name="gears">
                    <option value="">Alle</option>
                    @foreach($filterOptions['gears'] as $gears)
                        <option value="{{ $gears }}" @selected(($filters['gears'] ?? null) === $gears)>{{ $gears }}</option>
                    @endforeach
                </select>
            </label>
            <label>Pris fra
                <input type="number" name="price_min" min="0" step="100" value="{{ $filters['price_min'] ?? '' }}" placeholder="0">
            </label>
            <label>Pris til
                <input type="number" name="price_max" min="0" step="100" value="{{ $filters['price_max'] ?? '' }}" placeholder="80000">
            </label>
            <div class="flex items-end gap-3">
                <button class="rounded-full bg-rust px-7 py-4 font-extrabold text-white">Filtrer</button>
                @if(array_filter($filters))
                    <a href="{{ route('bikes.index') }}" class="rounded-full border-2 border-ink px-7 py-4 font-extrabold">Nullstill</a>
                @endif
            </div>
        </form>

        @if($bikes->count())
            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                @foreach($bikes as $bike)
                    <x-bike-card :bike="$bike" />
                @endforeach
            </div>
            <div class="mt-12">{{ $bikes->links() }}</div>
        @else
            <div class="card text-center">
                <h2>Ingen sykler matcher filteret</h2>
                <p class="lead">Prøv å justere filteret, eller send en melding og fortell hvilken størrelse og type du ser etter.</p>
                <a class="button" href="{{ route('home') }}#kontakt">Fortell hva du trenger</a>
            </div>
        @endif
    </div>
</section>
@endsection
