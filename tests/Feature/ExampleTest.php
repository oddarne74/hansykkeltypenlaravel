<?php

namespace Tests\Feature;

use App\Models\Bike;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontpage_caches_promoted_bikes_for_one_hour(): void
    {
        $this->travelTo(now());

        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(function (string $key, mixed $ttl, callable $callback): bool {
                if ($key !== Bike::FEATURED_BIKES_CACHE_KEY) {
                    return false;
                }

                if (! $ttl instanceof DateTimeInterface) {
                    return false;
                }

                return $ttl->getTimestamp() === now()->addHour()->getTimestamp();
            })
            ->andReturn(collect());

        $this->get(route('home'))->assertOk();
    }

    public function test_busts_frontpage_featured_bikes_cache_when_a_bike_is_created(): void
    {
        Cache::spy();

        Bike::factory()->create();

        Cache::shouldHaveReceived('forget')
            ->once()
            ->with(Bike::FEATURED_BIKES_CACHE_KEY);
    }

    public function test_busts_frontpage_featured_bikes_cache_when_a_bike_is_updated(): void
    {
        $bike = Bike::factory()->create();

        Cache::spy();

        $bike->update(['brand' => 'Updated Brand']);

        Cache::shouldHaveReceived('forget')
            ->once()
            ->with(Bike::FEATURED_BIKES_CACHE_KEY);
    }

    public function test_returns_a_successful_response(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Solide bruktsykler')
            ->assertDontSee('sykkelreparasjon');
    }
}
