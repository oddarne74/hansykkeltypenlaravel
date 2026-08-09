@props([
    'badge' => 'Bodø · Fauske · Salten',
    'title' => null,
    'description' => null,
    'primaryActionText' => 'Se sykler til salgs →',
    'primaryActionUrl' => null,
    'showServiceAction' => false,
    'serviceActionText' => 'Bestill service →',
    'serviceActionUrl' => null,
    'contactActionText' => 'Kontakt Han Sykkeltypen',
    'contactActionUrl' => '#kontakt',
])

@php
    $primaryActionUrl = $primaryActionUrl ?? route('bikes.index');
    $serviceActionUrl = $serviceActionUrl ?? route('service.create');
@endphp

<section class="stripe border-b-2 border-ink bg-forest px-5 py-20 text-white lg:px-8 lg:py-28">
    <div class="mx-auto max-w-7xl">
        <p class="inline-flex rounded-full border border-white/30 px-4 py-2 text-sm font-bold uppercase tracking-[.14em] text-sun">{{ $badge }}</p>
        <h1 class="mt-7 max-w-5xl font-display text-5xl uppercase leading-[.95] tracking-[-.045em] sm:text-6xl lg:text-8xl">
            {!! $title ?? $slot !!}
        </h1>
        @if($description)
            <p class="mt-8 max-w-2xl text-xl leading-8 text-white/80">{{ $description }}</p>
        @endif
        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <a href="{{ $primaryActionUrl }}" class="rounded-full bg-sun px-7 py-4 text-center font-extrabold text-ink shadow-hard">{{ $primaryActionText }}</a>
            @if($showServiceAction)
                <a href="{{ $serviceActionUrl }}" class="rounded-full bg-rust px-7 py-4 text-center font-extrabold text-white shadow-hard">{{ $serviceActionText }}</a>
            @endif
            <a href="{{ $contactActionUrl }}" class="rounded-full border-2 border-white px-7 py-4 text-center font-extrabold">{{ $contactActionText }}</a>
        </div>
    </div>
</section>
