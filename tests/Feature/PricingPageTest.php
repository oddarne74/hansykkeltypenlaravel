<?php

namespace Tests\Feature;

use App\Enums\Service;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    public function test_pricing_page_is_accessible_and_displays_services(): void
    {
        $response = $this->get(route('pricing'));

        $response->assertOk()
            ->assertViewIs('pricing');

        foreach (Service::cases() as $service) {
            $response->assertSee($service->value)
                ->assertSee($service->price())
                ->assertSee(route('service.create', ['service' => $service->value]));
        }
    }

    public function test_priser_alias_url_works(): void
    {
        $this->get('/priser')
            ->assertOk()
            ->assertViewIs('pricing');
    }
}
