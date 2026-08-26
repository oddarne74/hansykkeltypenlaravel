@extends('layouts.site')

@section('title', 'Brukte sykler på Rognan | Han Sykkeltypen')
@section('description', 'Han Sykkeltypen selger solide, oppussede bruktsykler på Rognan. Hver sykkel blir kontrollert, rengjort, justert og prøvekjørt før salg.')

@section('content')
<x-hero
    badge="Rognan · Salten"
    description="Er du på utkikk etter en god brukt sykkel på Rognan? Han Sykkeltypen pusser opp og selger solide bruktsykler for hverdag, skole, jobb og fritid."
>
    Solide bruktsykler på <span class="text-sun">Rognan</span>
</x-hero>

<section class="grain bg-paper px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Bruktsykler på Rognan</p>
            <h2>En brukt sykkel du kan stole på på Rognan</h2>
            <p class="lead">Han Sykkeltypen dekker store deler av Salten, inkludert Rognan, med solide bruktsykler som er klare for bruk.</p>
            <p class="mt-4">Hver sykkel er grundig gjennomgått, justert og prøvekjørt før salg.</p>
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
            <h2>Ser du etter sykkel på Rognan?</h2>
            <p class="lead">Send en melding om du ser etter en sykkel, eller har noe annet på hjertet.</p>
        </div>
        <x-contact-form />
    </div>
</section>
@endsection
