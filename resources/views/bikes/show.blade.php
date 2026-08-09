@extends('layouts.site')
@section('title', $bike->name . ' til salgs | Han Sykkeltypen')
@section('description', Str::limit($bike->description, 150))

@section('content')
    <section class="bg-forest px-5 py-14 text-white lg:px-8">
        <div class="mx-auto max-w-7xl">
            <a class="font-bold text-sun" href="{{ route('bikes.index') }}">← Alle sykler</a>
            <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_.8fr] lg:items-end">
                <div>
                    <p class="eyebrow text-sun">{{ $bike->type }}</p>
                    <h1 class="font-display text-5xl uppercase sm:text-7xl">{{ $bike->name }}</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">{{ $bike->description }}</p>
                </div>
                <div class="rounded-3xl border-2 border-white/20 p-7">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-sun px-3 py-1 text-xs font-black uppercase text-ink">{{ $bike->status }}</span>
                        <strong class="text-3xl">{{ number_format($bike->price, 0, ',', ' ') }} kr</strong>
                    </div>
                    <p class="mt-5">Str. {{ $bike->size }} · {{ $bike->rider_height }}</p>
                    
                    @if($bike->status === \App\Enums\BikeStatus::RESERVED)
                        <form action="{{ route('bikes.interest.store', $bike->slug) }}" method="POST" class="mt-6">
                            @csrf
                            <input type="email" name="email" required placeholder="Din e-post" class="w-full rounded-full border-2 border-white/20 bg-transparent px-4 py-3 placeholder:text-white/50">
                            <button type="submit" class="mt-3 block w-full rounded-full bg-sun px-6 py-4 text-center font-extrabold text-ink">Meld interesse</button>
                        </form>
                    @else
                        <a class="mt-6 block rounded-full bg-rust px-6 py-4 text-center font-extrabold" href="{{ route('home') }}#kontakt">Jeg er interessert</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    
    <section class="grain px-5 py-16 lg:px-8">
        <div class="mx-auto max-w-7xl">
            @php($main = $bike->images->first())
            <div class="overflow-hidden rounded-3xl border-2 border-ink bg-paper shadow-hard">
                @if($main)
                    <img src="{{ asset('storage/'.$main->path) }}" alt="{{ $main->alt }}" class="aspect-[16/9] w-full object-cover">
                @else
                    <div class="grid aspect-[16/9] place-items-center bg-sun text-8xl">🚲</div>
                @endif
            </div>
            
            <div class="mt-12 grid gap-10 lg:grid-cols-2">
                <div>
                    <p class="eyebrow">Detaljer</p>
                    <h2>Spesifikasjoner</h2>
                    <dl class="mt-7 divide-y-2 divide-ink/10 rounded-3xl border-2 border-ink bg-paper p-6">
                        @foreach(['Merke'=>$bike->brand,'Modell'=>$bike->model,'Type'=>$bike->type,'Rammestørrelse'=>$bike->size,'Passer høyde'=>$bike->rider_height,'Hjul'=>$bike->wheel_size,'Gir'=>$bike->gears,'Ramme'=>$bike->frame,'Bremser'=>$bike->brakes,'Farge'=>$bike->color,'Årsmodell'=>$bike->year] as $key=>$value)
                            <div class="flex justify-between gap-4 py-3">
                                <dt class="font-bold">{{ $key }}</dt>
                                <dd class="text-right text-ink/70">{{ $value ?: 'Ikke oppgitt' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div>
                    <p class="eyebrow">Verkstedloggen</p>
                    <h2>Dette er gjort</h2>
                    <div class="mt-7 space-y-4">
                        @foreach($bike->workItems as $item)
                            <article class="card">
                                <h3>{{ $item->title }}</h3>
                                <p>{{ $item->description }}</p>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-6 border-l-4 border-rust pl-5">
                        <strong>Tilstandsmerknader</strong>
                        <p class="mt-2 text-ink/70">{{ $bike->condition_notes }}</p>
                    </div>
                </div>
            </div>

            @if($bike->images->count()>1)
                <div class="mt-16">
                    <p class="eyebrow">Dokumentert arbeid</p>
                    <h2>Før og etter</h2>
                    <p class="lead max-w-3xl">Bildene viser sykkelen og detaljene før og etter klargjøring.</p>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($bike->images->skip(1) as $image)
                            <figure class="overflow-hidden rounded-3xl border-2 border-ink bg-paper">
                                <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt }}" class="aspect-[4/3] w-full object-cover">
                                <figcaption class="flex justify-between p-4 font-bold">
                                    <span>{{ $image->alt }}</span>
                                    <span class="text-rust">{{ $image->stage==='before'?'Før':'Etter' }}</span>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
