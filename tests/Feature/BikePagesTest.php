<?php

namespace Tests\Feature;

use App\Enums\BikeStatus;
use App\Models\Bike;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BikePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Solide bruktsykler')
            ->assertSee(route('service.create'));
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
}
