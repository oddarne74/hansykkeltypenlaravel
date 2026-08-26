<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use App\Filament\Resources\UnavailableWeeks\Pages\ListUnavailableWeeks;
use App\Mail\ServiceStatusUpdated;
use App\Models\ServiceRequest;
use App\Models\UnavailableWeek;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\ServiceRequestSeeder;
use Database\Seeders\UnavailableWeekSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_booking_page_redirects_to_home(): void
    {
        $this->get(route('service.create'))
            ->assertRedirect('/');
    }

    public function test_service_store_redirects_to_home(): void
    {
        $this->post(route('service.store'))
            ->assertRedirect('/');
    }

    public function test_admin_can_manage_unavailable_weeks(): void
    {
        $admin = User::factory()->create();
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        $this->actingAs($admin);

        Livewire::test(ListUnavailableWeeks::class)
            ->callAction('create', [
                'week_start' => $nextWeekStart,
                'reason' => 'Verksted stengt',
            ]);

        $this->assertDatabaseHas(UnavailableWeek::class, [
            'week_start' => $nextWeekStart,
            'reason' => 'Verksted stengt',
        ]);
    }

    public function test_admin_can_view_and_approve_service_request(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $serviceRequest = ServiceRequest::factory()->create([
            'status' => ServiceStatus::PENDING,
        ]);

        $this->actingAs($admin);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->id])
            ->callAction('approve');

        $this->assertSame(ServiceStatus::APPROVED, $serviceRequest->refresh()->status);

        Mail::assertQueued(ServiceStatusUpdated::class, function (ServiceStatusUpdated $mail) use ($serviceRequest) {
            return $mail->hasTo($serviceRequest->email)
                && $mail->serviceRequest->id === $serviceRequest->id;
        });
    }

    public function test_admin_can_deny_service_request(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $serviceRequest = ServiceRequest::factory()->create([
            'status' => ServiceStatus::PENDING,
        ]);

        $this->actingAs($admin);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->id])
            ->callAction('deny');

        $this->assertSame(ServiceStatus::DENIED, $serviceRequest->refresh()->status);

        Mail::assertQueued(ServiceStatusUpdated::class, function (ServiceStatusUpdated $mail) use ($serviceRequest) {
            return $mail->hasTo($serviceRequest->email)
                && $mail->serviceRequest->id === $serviceRequest->id;
        });
    }

    public function test_service_request_seeder_seeds_50_requests(): void
    {
        $this->seed(ServiceRequestSeeder::class);

        $this->assertDatabaseCount(ServiceRequest::class, 50);

        // Seeding again should be idempotent
        $this->seed(ServiceRequestSeeder::class);

        $this->assertDatabaseCount(ServiceRequest::class, 50);
    }

    public function test_unavailable_week_seeder_seeds_3_weeks(): void
    {
        $this->seed(UnavailableWeekSeeder::class);

        $this->assertDatabaseCount(UnavailableWeek::class, 3);

        // Seeding again should be idempotent
        $this->seed(UnavailableWeekSeeder::class);

        $this->assertDatabaseCount(UnavailableWeek::class, 3);
    }
}
