@extends('layouts.site')

@section('title', 'Bestill sykkelservice | Han Sykkeltypen')
@section('description', 'Bestill service på sykkelen din hos Han Sykkeltypen. Vi tar imot én sykkel per uke, med mulighet for henting og levering.')

@section('content')
<section class="stripe border-b-2 border-ink bg-forest px-5 py-20 text-white lg:px-8 lg:py-28">
    <div class="mx-auto max-w-7xl">
        <p class="inline-flex rounded-full border border-white/30 px-4 py-2 text-sm font-bold uppercase tracking-[.14em] text-sun">Sykkelservice</p>
        <h1 class="mt-7 max-w-5xl font-display text-5xl uppercase leading-[.95] tracking-[-.045em] sm:text-6xl lg:text-8xl">Bestill service <span class="text-sun">på sykkelen din</span></h1>
        <p class="mt-8 max-w-2xl text-xl leading-8 text-white/80">Vi tar imot én sykkel til service per uke for å gi hver sykkel den tiden den fortjener. Velg ønsket uke, beskriv hva som må gjøres, og last gjerne opp bilder av sykkelen.</p>
    </div>
</section>

<section class="grain bg-cream px-5 py-20 lg:px-8">
    <div class="mx-auto grid max-w-5xl gap-10 lg:grid-cols-[.8fr_1fr]">
        <div>
            <p class="eyebrow">Slik fungerer det</p>
            <h2>Én sykkel per uke</h2>
            <p class="lead">Send inn forespørselen din, så tar vi kontakt. Du får en e-post når forespørselen er godkjent eller avslått.</p>
            <div class="mt-8 space-y-4">
                <h3 class="text-xl font-bold">Våre servicetjenester</h3>
                @foreach(\App\Enums\Service::cases() as $type)
                    <article class="card">
                        <div class="flex items-baseline justify-between gap-2">
                            <h4 class="font-bold text-lg">{{ $type->value }}</h4>
                            <span class="font-extrabold text-rust">{{ $type->price() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-ink/80">{{ $type->typicalWork() }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <form method="post" action="{{ route('service.store') }}" enctype="multipart/form-data" class="card space-y-5">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

            <label>Navn<input name="name" value="{{ old('name') }}" required></label>
            <label>E-post<input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Telefon (valgfritt)<input name="phone" value="{{ old('phone') }}"></label>

            <label>Velg service
                <select name="service_type" required>
                    <option value="">Velg service</option>
                    @foreach(\App\Enums\Service::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('service_type') === $type->value)>
                            {{ $type->labelWithPrice() }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>Ønsket uke
                <select name="week_start" required>
                    <option value="">Velg uke</option>
                    @foreach($weeks as $value => $label)
                        <option value="{{ $value }}" @selected(old('week_start') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>Hva skal gjøres?<small class="block text-ink/60 font-normal">Beskriv hva som skal utføres eller fikses på sykkelen (påkrevd for "Annet").</small><textarea name="message" rows="6" required>{{ old('message') }}</textarea></label>

            <div class="space-y-1">
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="wants_pickup" value="1" class="mt-1 size-5" @checked(old('wants_pickup'))>
                    <span>Jeg ønsker henting og levering av sykkelen (etter avtale).</span>
                </label>
                <small class="block pl-8 text-ink/60 font-normal">Dersom du ikke ønsker henting, avtaler vi et tidspunkt for levering.</small>
            </div>

            <label>Adresse for henting (hvis aktuelt)<input name="address" value="{{ old('address') }}"></label>

            <label>Bilder av sykkelen (valgfritt)
                <input type="file" name="images[]" accept="image/*" multiple>
                <small class="text-ink/60">Legg gjerne ved bilder (opptil 6 bilder, maks 5 MB per bilde).</small>
            </label>

            @if($errors->any())
                <div class="rounded bg-red-100 p-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('status'))
                <div class="rounded bg-green-100 p-3 font-bold">{{ session('status') }}</div>
            @endif

            <button class="rounded-full bg-rust px-7 py-4 font-extrabold text-white">Send serviceforespørsel</button>
        </form>
    </div>
</section>
@endsection
