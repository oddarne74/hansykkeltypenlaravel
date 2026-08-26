@extends('layouts.site')

@section('title', 'Brukte sykler i Bodø | Han Sykkeltypen')
@section('description', 'Han Sykkeltypen selger solide, oppussede bruktsykler i Bodø. Hver sykkel blir kontrollert, rengjort, justert og prøvekjørt før salg.')

@section('content')
<x-hero
    badge="Bodø · Salten"
    description="Er du på utkikk etter en god brukt sykkel i Bodø? Han Sykkeltypen pusser opp og selger solide bruktsykler for hverdag, skole, jobb og fritid."
>
    Solide bruktsykler i <span class="text-sun">Bodø</span>
</x-hero>

<section class="grain bg-paper px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Bruktsykler i Bodø</p>
            <h2>En brukt sykkel du kan stole på i Bodø</h2>
            <p class="lead">Det kan være vanskelig å vite hva du får når du kjøper en brukt sykkel. Derfor går Han Sykkeltypen grundig gjennom hver sykkel før salg og utbedrer det som er nødvendig.</p>
            <p class="mt-4">Enten du bor i sentrum, på Mørkved, eller andre steder i Bodø-området, finner du solide og pålitelige sykler til hverdag og fritid.</p>
        </div>
        <ul class="grid gap-4 sm:grid-cols-2">
            @foreach(['Gir og bremser','Kjede, tannhjul og drivverk','Dekk, slanger og hjul','Lager og krank','Smøring og justering','Slitedeler ved behov','Grundig rengjøring','Prøvetur og sikkerhetskontroll'] as $item)
                <li class="rounded-2xl border-2 border-ink bg-cream p-5 font-bold shadow-hard">✓ {{ $item }}</li>
            @endforeach
        </ul>
    </div>
</section>

<section id="kontakt" class="grain bg-cream px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-5xl gap-10 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Ta kontakt</p>
            <h2>Ser du etter sykkel i Bodø?</h2>
            <p class="lead">Send en melding om du ser etter en sykkel, eller har noe annet på hjertet.</p>
        </div>
        <x-contact-form />
    </div>
</section>
@endsection
