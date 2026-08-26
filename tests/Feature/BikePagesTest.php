<?php

namespace Tests\Feature;

use App\Enums\BikeStatus;
use App\Models\Bike;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BikePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Solide bruktsykler')
            ->assertDontSee(route('service.create'))
            ->assertDontSee('Se sykler til salgs')
            ->assertDontSee(route('bikes.index'));
    }

    public function test_home_page_shows_bikes_link_when_bikes_are_for_sale(): void
    {
        $this->makeBike();

        $this->get('/')
            ->assertOk()
            ->assertSee('Se sykler til salgs')
            ->assertSee(route('bikes.index'));
    }

    public function test_home_page_shows_featured_bike(): void
    {
        $bike = $this->makeBike(['featured' => true]);
        $this->get('/')->assertOk()->assertSee($bike->name);
    }

    public function test_published_bike_has_detail_page(): void
    {
        $bike = Bike::create(['name' => 'Testsykkel', 'slug' => 'testsykkel', 'brand' => 'DBS', 'model' => 'Test', 'type' => 'Hybrid', 'price' => 2000, 'size' => 'M', 'description' => 'Test', 'published_at' => now()]);
        $this->get(route('bikes.show', $bike))->assertOk()->assertSee('Testsykkel');
    }

    public function test_contact_requires_consent(): void
    {
        $this->post('/kontakt', ['name' => 'Ola', 'contact' => 'ola@example.no', 'subject' => 'bike', 'message' => 'Hei'])->assertSessionHasErrors('consent');
    }

    private function makeBike(array $overrides = []): Bike
    {
        return Bike::create(array_merge([
            'name' => 'Trek FX 2',
            'slug' => 'trek-fx-2-'.uniqid(),
            'brand' => 'Trek',
            'model' => 'FX 2',
            'type' => 'Hybrid',
            'price' => 5000,
            'status' => 'Til salgs',
            'size' => 'M',
            'gears' => 'Shimano 2 x 9',
            'year' => '2022',
            'description' => 'Test',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_bikes_can_be_filtered_by_brand(): void
    {
        $trek = $this->makeBike();
        $giant = $this->makeBike(['name' => 'Giant Escape', 'slug' => 'giant-escape', 'brand' => 'Giant']);

        $this->get(route('bikes.index', ['brand' => 'Giant']))
            ->assertOk()
            ->assertSee($giant->name)
            ->assertDontSee($trek->name);
    }

    public function test_bikes_can_be_filtered_by_size(): void
    {
        $medium = $this->makeBike(['size' => 'M']);
        $large = $this->makeBike(['name' => 'Trek Marlin', 'slug' => 'trek-marlin', 'size' => 'L']);

        $this->get(route('bikes.index', ['size' => 'L']))
            ->assertOk()
            ->assertSee($large->name)
            ->assertDontSee($medium->name);
    }

    public function test_bikes_can_be_filtered_by_price_range(): void
    {
        $cheap = $this->makeBike(['price' => 2000]);
        $expensive = $this->makeBike(['name' => 'Trek Domane', 'slug' => 'trek-domane', 'price' => 15000]);

        $this->get(route('bikes.index', ['price_min' => 10000]))
            ->assertOk()
            ->assertSee($expensive->name)
            ->assertDontSee($cheap->name);

        $this->get(route('bikes.index', ['price_max' => 3000]))
            ->assertOk()
            ->assertSee($cheap->name)
            ->assertDontSee($expensive->name);
    }

    public function test_bikes_can_be_filtered_by_gears(): void
    {
        $matching = $this->makeBike(['gears' => 'SRAM 1 x 12']);
        $other = $this->makeBike(['name' => 'Trek Marlin', 'slug' => 'trek-marlin', 'gears' => 'Enkel gir']);

        $this->get(route('bikes.index', ['gears' => 'SRAM 1 x 12']))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name);
    }

    public function test_bike_page_shows_interested_button_when_for_sale(): void
    {
        $bike = $this->makeBike(['status' => BikeStatus::FOR_SALE]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('Jeg er interessert');
    }

    public function test_bike_page_shows_meld_interesse_form_when_reserved(): void
    {
        $bike = $this->makeBike(['status' => BikeStatus::RESERVED]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('Meld interesse');
    }

    public function test_bike_page_does_not_show_interested_button_when_sold(): void
    {
        $bike = $this->makeBike(['status' => BikeStatus::SOLD]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertDontSee('Jeg er interessert')
            ->assertDontSee('Meld interesse');
    }

    public function test_bike_with_in_progress_status_is_eligible_for_listing(): void
    {
        $bike = $this->makeBike(['name' => 'Under Arbeid Sykkel', 'slug' => 'under-arbeid-sykkel', 'status' => BikeStatus::IN_PROGRESS]);

        $this->get(route('bikes.index'))
            ->assertOk()
            ->assertSee('Under Arbeid Sykkel');

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('Jeg er interessert');
    }

    public function test_bike_description_formatting_preserves_line_breaks(): void
    {
        $bike = $this->makeBike([
            'description' => "Første linje\nAndre linje",
            'condition_notes' => "Merknad 1\nMerknad 2",
        ]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('Første linje<br />', false)
            ->assertSee('Andre linje', false)
            ->assertSee('Merknad 1<br />', false);
    }

    public function test_bike_detail_page_renders_alpine_lightbox_for_images(): void
    {
        $bike = $this->makeBike();
        $bike->images()->create([
            'path' => 'images/bikes/before.jpg',
            'alt' => 'Sykkelen før klargjøring',
            'stage' => 'before',
            'sort_order' => 1,
        ]);
        $bike->images()->create([
            'path' => 'images/bikes/after.jpg',
            'alt' => 'Sykkelen etter klargjøring',
            'stage' => 'after',
            'sort_order' => 2,
        ]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('x-data="lightbox', false)
            ->assertSee('Alpine.data(\'lightbox\'', false)
            ->assertSee('Sykkelen før klargjøring')
            ->assertSee('Sykkelen etter klargjøring')
            ->assertSee('after.jpg', false);
    }

    public function test_bike_detail_page_without_images_does_not_render_lightbox(): void
    {
        $bike = $this->makeBike();

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertDontSee('x-data="lightbox', false);
    }

    public function test_bike_detail_page_lightbox_uses_large_image_for_main_display(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $bike = $this->makeBike();
        Storage::disk('local')->put('originals/bikes/'.$bike->slug.'/photo.jpg', 'fake-bytes');
        Storage::disk('public')->put('images/bikes/'.$bike->slug.'/photo-thumb.webp', 'thumb-bytes');
        Storage::disk('public')->put('images/bikes/'.$bike->slug.'/photo-large.webp', 'large-bytes');

        $image = $bike->images()->create([
            'path' => 'originals/bikes/'.$bike->slug.'/photo.jpg',
            'thumbnail_path' => 'images/bikes/'.$bike->slug.'/photo-thumb.webp',
            'large_path' => 'images/bikes/'.$bike->slug.'/photo-large.webp',
            'alt' => 'Testbilde',
            'stage' => 'after',
            'sort_order' => 1,
        ]);

        $this->get(route('bikes.show', $bike))
            ->assertOk()
            ->assertSee('photo-large.webp')
            ->assertSee('photo-thumb.webp');
    }

    public function test_bike_card_uses_the_optimized_thumbnail_instead_of_the_private_original(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $bike = $this->makeBike();
        Storage::disk('local')->put('originals/bikes/'.$bike->slug.'/photo.jpg', 'fake-bytes');
        Storage::disk('public')->put('images/bikes/'.$bike->slug.'/photo-thumb.webp', 'thumb-bytes');
        Storage::disk('public')->put('images/bikes/'.$bike->slug.'/photo-large.webp', 'large-bytes');

        $image = $bike->images()->create([
            'path' => 'originals/bikes/'.$bike->slug.'/photo.jpg',
            'thumbnail_path' => 'images/bikes/'.$bike->slug.'/photo-thumb.webp',
            'large_path' => 'images/bikes/'.$bike->slug.'/photo-large.webp',
            'alt' => 'Testbilde',
            'stage' => 'after',
            'sort_order' => 1,
        ]);

        $this->get(route('bikes.index'))
            ->assertOk()
            ->assertSee($image->thumbnail_url, false)
            ->assertDontSee('originals/bikes', false);
    }

    public function test_in_progress_bike_is_promoted_on_front_page_when_no_other_bikes_are_available(): void
    {
        $bike = $this->makeBike([
            'name' => 'Prosjekt Sykkel',
            'slug' => 'prosjekt-sykkel',
            'status' => BikeStatus::IN_PROGRESS,
            'featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Prosjekt Sykkel');

        $forSaleBike = $this->makeBike([
            'name' => 'Til Salgs Sykkel',
            'slug' => 'til-salgs-sykkel',
            'status' => BikeStatus::FOR_SALE,
            'featured' => true,
        ]);

        Cache::forget(Bike::FEATURED_BIKES_CACHE_KEY);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Til Salgs Sykkel')
            ->assertDontSee('Prosjekt Sykkel');
    }
}
