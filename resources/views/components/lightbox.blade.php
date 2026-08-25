@props([
    // Array of images. Each item: ['src' => large url, 'thumb' => thumbnail url, 'alt' => alt text, 'caption' => optional caption]
    'images' => [],
])

@php
    $items = collect($images)
        ->map(fn ($image) => [
            'src' => $image['src'] ?? ($image['thumb'] ?? ''),
            'thumb' => $image['thumb'] ?? ($image['src'] ?? ''),
            'alt' => $image['alt'] ?? '',
            'caption' => $image['caption'] ?? '',
        ])
        ->values();
@endphp

@if($items->isNotEmpty())
    <div
        x-data="lightbox(@js($items->all()))"
        @keydown.window.arrow-right="next()"
        @keydown.window.arrow-left="prev()"
        @keydown.window.escape="close()"
        {{ $attributes }}
    >
        {{-- Featured main image trigger --}}
        <figure
            class="group relative cursor-pointer overflow-hidden rounded-3xl border-2 border-ink bg-paper shadow-hard focus-ring"
            role="button"
            tabindex="0"
            aria-label="Vis bilde i fullskjerm"
            @click="open(current)"
            @keydown.enter.prevent="open(current)"
            @keydown.space.prevent="open(current)"
        >
            <div class="relative flex min-h-[350px] max-h-[450px] w-full items-center justify-center overflow-hidden bg-forest lg:max-h-[520px]">
                <img :src="images[current].src" :alt="images[current].alt" class="max-h-[450px] w-full object-contain transition duration-500 group-hover:scale-105 lg:max-h-[520px]">
                <template x-if="images[current].caption">
                    <span class="absolute left-4 top-4 rounded-full bg-rust px-3 py-1 text-xs font-black uppercase text-white" x-text="images[current].caption"></span>
                </template>
                <span class="absolute bottom-4 right-4 rounded-full bg-ink/70 px-3 py-1 text-xs font-bold text-white opacity-0 transition group-hover:opacity-100">Klikk for fullskjerm</span>
            </div>
        </figure>

        {{-- Thumbnail strip triggers --}}
        <div class="mt-4 flex gap-3 overflow-x-auto pb-1" x-show="images.length > 1">
            <template x-for="(image, index) in images" :key="'trigger-' + index">
                <button
                    type="button"
                    class="aspect-[4/3] w-28 shrink-0 overflow-hidden rounded-2xl border-2 transition focus-ring"
                    :class="index === current ? 'border-ink ring-2 ring-sun' : 'border-ink/20 opacity-70 hover:opacity-100'"
                    :aria-label="'Vis bilde ' + (index + 1)"
                    @click="go(index)"
                    @dblclick="open(index)"
                >
                    <img :src="image.thumb" :alt="image.alt" class="h-full w-full object-cover" loading="lazy">
                </button>
            </template>
        </div>

        {{-- Full-screen lightbox overlay --}}
        <div
            x-show="isOpen"
            x-transition.opacity
            style="display:none"
            class="fixed inset-0 z-50 flex flex-col bg-ink/95 p-4 backdrop-blur-md md:p-8"
            role="dialog"
            aria-modal="true"
            aria-label="Bildevisning"
            @click.self="close()"
        >
            {{-- Top bar --}}
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between text-white">
                <span class="rounded-full bg-white/20 px-3 py-1 text-sm font-bold">
                    <span x-text="current + 1"></span> / <span x-text="images.length"></span>
                </span>
                <button
                    type="button"
                    class="grid size-12 place-items-center rounded-full bg-white/10 text-2xl font-bold text-white transition hover:bg-white/20 focus-ring"
                    aria-label="Lukk"
                    @click="close()"
                >&times;</button>
            </div>

            {{-- Image stage with swipe support --}}
            <div
                class="relative mx-auto my-4 flex w-full max-w-7xl flex-1 items-center justify-center"
                :class="isFullSize ? 'overflow-auto items-start justify-start p-8 md:p-16' : 'overflow-hidden items-center justify-center'"
                @touchstart.passive="touchStart($event)"
                @touchend.passive="touchEnd($event)"
            >
                <button
                    type="button"
                    class="absolute left-2 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-full border-2 border-white/20 bg-ink/80 text-2xl font-bold text-white shadow-xl transition hover:border-ink hover:bg-sun hover:text-ink focus-ring md:left-6 md:size-14"
                    aria-label="Forrige bilde"
                    x-show="images.length > 1 && !isFullSize"
                    @click="prev()"
                >&larr;</button>

                <figure class="flex max-h-full flex-col items-center justify-center gap-4">
                    <img
                        :src="images[current].src"
                        :alt="images[current].alt"
                        @click.stop="isFullSize = !isFullSize"
                        :class="isFullSize ? 'max-h-none max-w-none cursor-zoom-out' : 'max-h-[75vh] max-w-full cursor-zoom-in'"
                        class="rounded-2xl object-contain shadow-2xl transition-all duration-200"
                        :title="isFullSize ? 'Klikk for å tilpasse' : 'Klikk for full størrelse'"
                    >
                    <figcaption class="text-center text-sm font-medium text-white/80" x-show="(images[current].alt || images[current].caption) && !isFullSize">
                        <span x-text="images[current].alt"></span>
                        <template x-if="images[current].caption">
                            <span class="text-sun" x-text="' — ' + images[current].caption"></span>
                        </template>
                    </figcaption>
                </figure>

                <button
                    type="button"
                    class="absolute right-2 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-full border-2 border-white/20 bg-ink/80 text-2xl font-bold text-white shadow-xl transition hover:border-ink hover:bg-sun hover:text-ink focus-ring md:right-6 md:size-14"
                    aria-label="Neste bilde"
                    x-show="images.length > 1 && !isFullSize"
                    @click="next()"
                >&rarr;</button>
            </div>

            {{-- Thumbnail strip --}}
            <div class="mx-auto flex w-full max-w-4xl justify-center gap-2 overflow-x-auto py-2" x-show="images.length > 1">
                <template x-for="(image, index) in images" :key="'strip-' + index">
                    <button
                        type="button"
                        class="size-16 shrink-0 overflow-hidden rounded-xl border-2 transition focus-ring"
                        :class="index === current ? 'border-sun opacity-100 ring-2 ring-sun' : 'border-white/20 opacity-65 hover:opacity-100'"
                        :aria-label="'Gå til bilde ' + (index + 1)"
                        @click="go(index)"
                    >
                        <img :src="image.thumb" :alt="image.alt" class="h-full w-full object-cover">
                    </button>
                </template>
            </div>
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('lightbox', (images) => ({
                        images: images,
                        isOpen: false,
                        current: 0,
                        touchStartX: 0,
                        open(index) {
                            this.current = index;
                            this.isOpen = true;
                            document.body.style.overflow = 'hidden';
                        },
                        close() {
                            this.isOpen = false;
                            document.body.style.overflow = '';
                        },
                        go(index) {
                            this.current = index;
                        },
                        next() {
                            if (! this.isOpen) return;
                            this.current = (this.current + 1) % this.images.length;
                        },
                        prev() {
                            if (! this.isOpen) return;
                            this.current = (this.current - 1 + this.images.length) % this.images.length;
                        },
                        touchStart(event) {
                            this.touchStartX = event.changedTouches[0].screenX;
                        },
                        touchEnd(event) {
                            const delta = event.changedTouches[0].screenX - this.touchStartX;
                            const threshold = 50;
                            if (delta < -threshold) this.next();
                            if (delta > threshold) this.prev();
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endif
