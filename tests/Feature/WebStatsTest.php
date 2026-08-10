<?php

namespace Tests\Feature;

use App\Filament\Resources\WebStats\Pages\ListWebStats;
use App\Filament\Resources\WebStats\Pages\ViewWebStat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use WebStats\Models\WebStat;
use WebStats\Models\WebStatHit;

class WebStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_page_tracks_web_stat_and_hit(): void
    {
        $this->get(route('home'))
            ->assertOk();

        $this->assertDatabaseHas(WebStat::class, [
            'url' => route('home'),
        ]);

        $webStat = WebStat::where('url', route('home'))->first();
        $this->assertNotNull($webStat);
        $this->assertEquals(1, $webStat->total_views);
        $this->assertEquals(1, $webStat->views_today);
        $this->assertEquals(1, $webStat->views_this_week);
        $this->assertEquals(1, $webStat->views_this_month);

        $this->assertDatabaseHas(WebStatHit::class, [
            'web_stat_id' => $webStat->id,
        ]);
    }

    public function test_admin_can_view_web_stats_resource(): void
    {
        $user = User::factory()->create();
        $webStat = WebStat::create([
            'url' => 'https://hansykkeltypen.cc/test-page',
            'title' => 'Test Page Title',
        ]);
        WebStatHit::create([
            'web_stat_id' => $webStat->id,
        ]);

        Livewire::actingAs($user)
            ->test(ListWebStats::class)
            ->assertCanSeeTableRecords([$webStat])
            ->searchTable('Test Page');

        Livewire::actingAs($user)
            ->test(ViewWebStat::class, [
                'record' => $webStat->getRouteKey(),
            ])
            ->assertSuccessful()
            ->assertSee('Test Page Title');
    }
}
