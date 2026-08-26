@extends('layouts.site')

@section('title', 'Brukte sykler i Fauske | Han Sykkeltypen')
@section('description', 'Han Sykkeltypen selger solide, oppussede bruktsykler i Fauske. Hver sykkel blir kontrollert, rengjort, justert og prøvekjørt før salg.')

@section('content')
<x-hero
    badge="Fauske · Salten"
    description="Er du på utkikk etter en god brukt sykkel i Fauske? Han Sykkeltypen pusser opp og selger solide bruktsykler for hverdag, skole, jobb og fritid."
>
    Solide bruktsykler i <span class="text-sun">Fauske</span>
</x-hero>

<section class="grain bg-paper px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Bruktsykler i Fauske</p>
            <h2>En brukt sykkel du kan stole på i Fauske</h2>
            <p class="lead">Han Sykkeltypen leverer solide og gjennomgåtte bruktsykler til kunder i Fauske og omegn.</p>
            <p class="mt-4">Hver sykkel blir kontrollert, rengjort, justert og prøvekjørt slik at den er klar for veien fra første dag.</p>
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
            <h2>Ser du etter sykkel i Fauske?</h2>
            <p class="lead">Send en melding om du ser etter en sykkel, eller har noe annet på hjertet.</p>
        </div>
        <x-contact-form />
    </div>
</section>
@endsection
