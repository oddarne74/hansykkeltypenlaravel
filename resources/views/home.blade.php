@extends('layouts.site')
@section('content')
<section class="stripe border-b-2 border-ink bg-forest px-5 py-20 text-white lg:px-8 lg:py-28"><div class="mx-auto max-w-7xl"><p class="inline-flex rounded-full border border-white/30 px-4 py-2 text-sm font-bold uppercase tracking-[.14em] text-sun">Bodø · Fauske · Salten</p><h1 class="mt-7 max-w-5xl font-display text-5xl uppercase leading-[.95] tracking-[-.045em] sm:text-6xl lg:text-8xl">Solide bruktsykler <span class="text-sun">– klargjort</span> for mange nye kilometer</h1><p class="mt-8 max-w-2xl text-xl leading-8 text-white/80">Han Sykkeltypen pusser opp og selger solide bruktsykler for hverdag, skole, jobb og fritid. Hver sykkel blir kontrollert, rengjort, justert og prøvekjørt før salg.</p><div class="mt-9 flex flex-col gap-3 sm:flex-row"><a href="{{ route('bikes.index') }}" class="rounded-full bg-sun px-7 py-4 text-center font-extrabold text-ink shadow-hard">Se sykler til salgs →</a><a href="#kontakt" class="rounded-full border-2 border-white px-7 py-4 text-center font-extrabold">Kontakt Han Sykkeltypen</a></div></div></section>
<section class="border-b-2 border-ink bg-sun"><div class="mx-auto grid max-w-7xl grid-cols-2 lg:grid-cols-4">@foreach(['Kontrollert'=>'fra styre til bakhjul','Justert'=>'gir og bremser','Rengjort'=>'og smurt','Prøvekjørt'=>'før den selges'] as $title=>$text)<div class="border-ink px-5 py-6 text-center odd:border-r-2 lg:border-r-2"><strong class="block font-display text-2xl uppercase">{{ $title }}</strong><span class="text-sm">{{ $text }}</span></div>@endforeach</div></section>

@if($featured->isNotEmpty())
<section class="grain bg-stone-100 px-5 py-20 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <h2 class="mb-10 text-center font-display text-4xl uppercase">Utvalgte sykler</h2>
        <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory">
            @foreach($featured as $bike)
                <div class="w-full min-w-75 max-w-md shrink-0 snap-start">
                    <x-bike-card :bike="$bike" />
                </div>
            @endforeach
            <div class="w-full min-w-75 max-w-md shrink-0 snap-start">
                <a href="{{ route('bikes.index') }}" class="flex h-full min-h-75 flex-col items-center justify-center rounded-3xl border-2 border-dashed border-ink/30 bg-paper p-6 text-center shadow-hard transition hover:border-ink/60 hover:bg-ink/5">
                    <span class="font-display text-2xl uppercase">Se alle sykler</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endif
<section class="grain bg-paper px-5 py-20 lg:px-8"><div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2"><div><p class="eyebrow">Slik jobber jeg</p><h2>En brukt sykkel du kan stole på</h2><p class="lead">Det kan være vanskelig å vite hva du får når du kjøper en brukt sykkel. Derfor går jeg gjennom sykkelen før salg og utbedrer det som er nødvendig.</p></div><ul class="grid gap-4 sm:grid-cols-2">@foreach(['Gir og bremser','Kjede, tannhjul og drivverk','Dekk, slanger og hjul','Lager og krank','Smøring og justering','Slitedeler ved behov','Grundig rengjøring','Prøvetur og sikkerhetskontroll'] as $item)<li class="rounded-2xl border-2 border-ink bg-cream p-5 font-bold shadow-hard">✓ {{ $item }}</li>@endforeach</ul></div></section>
<section id="service" class="border-y-2 border-ink bg-cream px-5 py-20 lg:px-8"><div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2"><div><p class="eyebrow">Sykkelservice</p><h2>Enkel service og vedlikehold</h2><p class="lead">Har du allerede en sykkel som trenger litt omsorg? Jeg tilbyr enkelt vedlikehold og reparasjoner av vanlige sykler (tar imot én sykkel per uke).</p><div class="mt-8"><a href="{{ route('service.create') }}" class="inline-block rounded-full bg-rust px-7 py-4 font-extrabold text-white">Bestill service her →</a></div></div><ul class="card grid gap-3 sm:grid-cols-2">@foreach(\App\Enums\Service::cases() as $type)<li><strong class="font-bold">{{ $type->value }}</strong> — {{ $type->price() }}</li>@endforeach</ul></div></section>
<section id="henting" class="bg-paper px-5 py-20 lg:px-8"><div class="mx-auto max-w-7xl"><p class="eyebrow">Enklere sykkelservice</p><h2>Jeg kan hente og levere sykkelen</h2><p class="lead max-w-3xl">Etter avtale kan jeg hente sykkelen hjemme hos deg, utføre avtalt service og levere den tilbake når den er klar. Pris og tilgjengelighet avtales på forhånd og avhenger av avstand og rute.</p><div class="mt-10 grid gap-5 md:grid-cols-3">@foreach(['Avtal oppdraget'=>'Send beskrivelse, bilder og poststed.','Sykkelen hentes'=>'Vi avtaler et tidspunkt som passer.','Service og retur'=>'Du godkjenner tillegg før arbeid utføres.'] as $title=>$text)<article class="card"><span class="eyebrow">0{{ $loop->iteration }}</span><h3>{{ $title }}</h3><p>{{ $text }}</p></article>@endforeach</div></div></section>
<section id="om" class="bg-forest px-5 py-20 text-white lg:px-8"><div class="mx-auto max-w-4xl"><p class="eyebrow text-sun">Lokalt i Salten</p><h2>Han Sykkeltypen</h2><p class="mt-6 text-lg leading-8 text-white/75">Et lite, lokalt initiativ for sykler som er praktiske, holdbare og mulige å vedlikeholde. Virksomheten betjener Bodø, Fauske, Sørfold, Saltdal, Gildeskål, Beiarn, Steigen og Hamarøy.</p></div></section>
<section id="kontakt" class="grain bg-cream px-5 py-20 lg:px-8"><div class="mx-auto grid max-w-5xl gap-10 lg:grid-cols-2"><div><p class="eyebrow">Ta kontakt</p><h2>Ser du etter en sykkel, eller trenger du service?</h2><p class="lead">Oppgi høyde dersom du ser etter sykkel. Beskriv problemet og send gjerne bilder dersom du ønsker service.</p></div><x-contact-form /></div></section>
@endsection
