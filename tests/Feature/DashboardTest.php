<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_redirects_to_the_admin_panel(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/admin');
    }

    public function test_guests_are_redirected_to_the_admin_login_page(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_authenticated_users_can_visit_the_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin');
        $response->assertOk();
    }
}
