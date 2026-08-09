<!doctype html><html lang="nb"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Han Sykkeltypen | Brukte sykler og sykkelservice i Salten')</title><meta name="description" content="@yield('description','Solide bruktsykler og enkel sykkelservice i Bodø, Fauske og Salten.')"><meta name="theme-color" content="#173f35">@vite(['resources/css/app.css','resources/js/app.js'])@stack('head')</head>
<body class="bg-cream font-sans text-ink antialiased"><a href="#innhold" class="focus-ring fixed left-4 top-4 z-50 -translate-y-24 rounded bg-sun px-4 py-3 font-bold focus:translate-y-0">Hopp til innhold</a>
<header class="border-b-2 border-ink bg-paper"><div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8"><a href="{{ route('home') }}" class="focus-ring flex items-center gap-3"><span class="grid size-11 place-items-center rounded-full border-2 border-ink bg-sun text-2xl">⚙</span><span><strong class="block font-display text-lg uppercase leading-none">Han Sykkeltypen</strong><small class="mt-1 block font-bold uppercase tracking-[.18em] text-moss">Brukt. Fikset. Klar.</small></span></a><nav class="hidden items-center gap-7 text-sm font-bold md:flex"><a href="{{ route('bikes.index') }}">Sykler</a><a href="{{ route('service.create') }}">Bestill service</a><a href="{{ route('home') }}#henting">Henting</a><a href="{{ route('home') }}#om">Om</a><a class="rounded-full bg-rust px-5 py-3 text-white" href="{{ route('home') }}#kontakt">Ta kontakt</a></nav><a class="rounded-full bg-rust px-4 py-2.5 text-sm font-bold text-white md:hidden" href="{{ route('home') }}#kontakt">Kontakt</a></div></header>
<main id="innhold">@yield('content')</main>
<footer class="border-t-2 border-ink bg-ink px-5 py-10 text-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <strong class="font-display text-xl uppercase text-sun">Han Sykkeltypen</strong>
            <p class="mt-1 text-sm text-white/60">Brukte sykler og sykkelservice i Bodø, Fauske og Salten.</p>
        </div>
        <div class="grid grid-cols-2 gap-8 sm:flex sm:gap-12">
            <nav class="flex flex-col gap-2 text-sm">
                <strong class="text-xs font-bold uppercase tracking-widest text-white/40">Meny</strong>
                <a href="{{ route('bikes.index') }}" class="font-bold text-sun">Sykler til salgs</a>
                <a href="{{ route('service.create') }}" class="hover:text-sun">Bestill service</a>
                <a href="{{ route('home') }}#kontakt" class="hover:text-sun">Kontakt</a>
            </nav>
            <nav class="flex flex-col gap-2 text-sm">
                <strong class="text-xs font-bold uppercase tracking-widest text-white/40">Områder</strong>
                <a href="{{ route('locations.bodo') }}" class="hover:text-sun">Bodø</a>
                <a href="{{ route('locations.fauske') }}" class="hover:text-sun">Fauske</a>
                <a href="{{ route('locations.rognan') }}" class="hover:text-sun">Rognan</a>
            </nav>
        </div>
    </div>
</footer>
</body></html>
