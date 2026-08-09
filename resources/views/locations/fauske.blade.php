@extends('layouts.site')

@section('title', 'Brukte sykler og sykkelservice i Fauske | Han Sykkeltypen')
@section('description', 'Han Sykkeltypen tilbyr solide bruktsykler og sykkelservice i Fauske. Vi henter og leverer sykkelen din etter avtale.')

@section('content')
<x-hero
    badge="Fauske · Salten"
    :show-service-action="true"
    description="Trenger du sykkelservice eller er på utkikk etter en god brukt sykkel i Fauske? Han Sykkeltypen pusser opp og selger solide bruktsykler, og utfører vedlikehold som gjør sykkelen din klar for veien igjen."
>
    Sykkelservice og brukte sykler i <span class="text-sun">Fauske</span>
</x-hero>

<section class="grain bg-paper px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Sykkelservice i Fauske</p>
            <h2>Vi henter sykkelen din i Fauske</h2>
            <p class="lead">Det skal være enkelt å gi sykkelen den omsorgen den trenger. Bor du i Fauske, kan vi avtale henting og levering av sykkelen din for service.</p>
            <p class="mt-4">Han Sykkeltypen er ofte i Fauske-området og kan hente sykkelen din hjemme hos deg, utføre service, og levere den tilbake når den er klar.</p>
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
            <h2>Trenger du hjelp med sykkelen din i Fauske?</h2>
            <p class="lead">Send en melding om du ser etter en ny sykkel, eller trenger service på den du har.</p>
        </div>
        <x-contact-form />
    </div>
</section>
@endsection
