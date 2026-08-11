@extends('layouts.site')

@section('title', 'Priser på sykkelservice og sykkelreparasjon | Han Sykkeltypen')
@section('description', 'Oversikt over våre priser og pakker for sykkelservice og sykkelreparasjon i Bodø, Fauske og Salten. Alt fra enkel sikkerhetssjekk til grundig overhaling.')

@section('content')
<x-hero
    badge="Oversikt over priser"
    title="Priser på <span class='text-sun'>sykkelservice</span> og sykkelreparasjon"
    description="Vi tilbyr faste og forutsigbare priser på sykkelservice og sykkelreparasjon. Se gjennom våre servicetjenester under og bestill tid for din sykkel."
    primaryActionText="Bestill service nå →"
    :primaryActionUrl="route('service.create')"
    contactActionText="Ta kontakt ved spørsmål"
    :contactActionUrl="route('home') . '#kontakt'"
/>

<section class="grain bg-cream px-5 py-20 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="text-center">
            <p class="eyebrow">Servicepakker & Priser</p>
            <h2>Våre servicetjenester</h2>
            <p class="lead mx-auto max-w-2xl">
                Vi tar imot én sykkel per uke for å sikre at hver sykkel får den grundigheten den fortjener.
            </p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $service)
                <article class="card flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 border-b-2 border-ink/10 pb-4">
                            <h3 class="text-xl font-bold">{{ $service->value }}</h3>
                            <span class="rounded-full bg-rust/10 px-3 py-1 font-extrabold text-rust text-base shrink-0">
                                {{ $service->price() }}
                            </span>
                        </div>
                        <p class="mt-4 text-sm leading-relaxed text-ink/80">
                            {{ $service->typicalWork() }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4">
                        <a href="{{ route('service.create', ['service' => $service->value]) }}" class="inline-flex w-full justify-center rounded-full bg-forest px-5 py-3 text-center text-sm font-bold text-white transition hover:bg-moss">
                            Bestill {{ $service->value }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="card mt-12 bg-paper p-8 text-center border-2 border-ink">
            <h3 class="text-2xl font-bold">Tilleggstjeneste</h3>
            <p class="mt-2 text-ink/80">
                Mulighet for henting og levering i Bodø, Fauske og Saltdal etter avtale.
            </p>
            <p class="mt-2 text-ink/80">
                Priseksempel når hentestedet ikke er fryktelig langt fra E6 / RV80.<br>
                <strong>Bodø</strong>(til Løpsmark): 450 kr, <strong>Fauske</strong>: 300 kr, <strong>Saltdal</strong> (til Røkland): 450 kr. Annet etter avtale.
            </p>
            <p class="mt-2 text-ink/80">
                Annet etter avtale.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('service.create') }}" class="rounded-full bg-rust px-7 py-3 font-bold text-white">
                    Gå til bestilling →
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
