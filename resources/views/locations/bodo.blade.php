@extends('layouts.app')

@section('title', 'Brukte sykler og sykkelservice i Bodø | Han Sykkeltypen')
@section('description', 'Han Sykkeltypen tilbyr solide bruktsykler og sykkelservice i Bodø. Vi henter og leverer sykkelen din etter avtale.')

@section('content')
<section class="stripe border-b-2 border-ink bg-forest px-5 py-20 text-white lg:px-8 lg:py-28">
    <div class="mx-auto max-w-7xl">
        <p class="inline-flex rounded-full border border-white/30 px-4 py-2 text-sm font-bold uppercase tracking-[.14em] text-sun">Bodø · Salten</p>
        <h1 class="mt-7 max-w-5xl font-display text-5xl uppercase leading-[.95] tracking-[-.045em] sm:text-6xl lg:text-8xl">Sykkelservice og brukte sykler i <span class="text-sun">Bodø</span></h1>
        <p class="mt-8 max-w-2xl text-xl leading-8 text-white/80">Trenger du sykkelservice eller er på utkikk etter en god brukt sykkel i Bodø? Han Sykkeltypen pusser opp og selger solide bruktsykler, og utfører vedlikehold som gjør sykkelen din klar for veien igjen.</p>
        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('bikes.index') }}" class="rounded-full bg-sun px-7 py-4 text-center font-extrabold text-ink shadow-hard">Se sykler til salgs →</a>
            <a href="#kontakt" class="rounded-full border-2 border-white px-7 py-4 text-center font-extrabold">Kontakt Han Sykkeltypen</a>
        </div>
    </div>
</section>

<section class="grain bg-paper px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2">
        <div>
            <p class="eyebrow">Sykkelservice i Bodø</p>
            <h2>Vi henter sykkelen din i Bodø</h2>
            <p class="lead">Det skal være enkelt å gi sykkelen den omsorgen den trenger. Bor du i Bodø, kan vi avtale henting og levering av sykkelen din for service.</p>
            <p class="mt-4">Enten du bor i sentrum, på Mørkved, eller andre steder i Bodø-området, kan Han Sykkeltypen hjelpe deg med alt fra enkle justeringer til sesongklargjøring.</p>
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
            <h2>Trenger du hjelp med sykkelen din i Bodø?</h2>
            <p class="lead">Send en melding om du ser etter en ny sykkel, eller trenger service på den du har.</p>
        </div>
        <form method="post" action="{{ route('contact.store') }}" class="card space-y-5">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
            <label>Navn<input name="name" value="{{ old('name') }}" required></label>
            <label>E-post eller telefon<input name="contact" value="{{ old('contact') }}" required></label>
            <label>Hva gjelder det?
                <select name="subject" required>
                    <option value="bike">Jeg ser etter en sykkel</option>
                    <option value="service">Service eller reparasjon</option>
                    <option value="sell">Selge eller gi bort en sykkel</option>
                    <option value="other">Annet</option>
                </select>
            </label>
            <label>Melding<textarea name="message" rows="6" required>{{ old('message') }}</textarea></label>
            <label class="flex items-start gap-3">
                <input type="checkbox" name="consent" value="1" required class="mt-1 size-5">
                <span>Jeg samtykker til at opplysningene brukes til å besvare henvendelsen.</span>
            </label>
            @if($errors->any())
                <div class="rounded bg-red-100 p-3 text-sm text-red-800">Sjekk at alle felt er riktig utfylt.</div>
            @endif
            @if(session('status'))
                <div class="rounded bg-green-100 p-3 font-bold">{{ session('status') }}</div>
            @endif
            <button class="rounded-full bg-rust px-7 py-4 font-extrabold text-white">Send henvendelse</button>
        </form>
    </div>
</section>
@endsection
