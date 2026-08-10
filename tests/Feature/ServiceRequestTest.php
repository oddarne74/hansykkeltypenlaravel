<?php

namespace Tests\Feature;

use App\Enums\Service;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_view_service_booking_page(): void
    {
        $this->get(route('service.create'))
            ->assertOk()
            ->assertSee('Bestill service')
            ->assertSee('én sykkel per uke')
            ->assertSee('Sykkelsjekk')
            ->assertSee('349 kr')
            ->assertSee('Enkel service')
            ->assertSee('649 kr')
            ->assertSee('Grundig Service')
            ->assertSee('1 299 kr')
            ->assertSee('Annet')
            ->assertSee('Etter avtale')
            ->assertSee('Dersom du ikke ønsker henting, avtaler vi et tidspunkt for levering.');
    }

    public function test_service_booking_page_defaults_to_enkel_service_when_no_service_selected(): void
    {
        $response = $this->get(route('service.create'));

        $response->assertOk()
            ->assertSee('<option value="Enkel service" selected>', false);
    }

    public function test_service_booking_page_prepopulates_selected_service_from_query(): void
    {
        $response = $this->get(route('service.create', ['service' => 'Grundig Service']));

        $response->assertOk()
            ->assertSee('<option value="Grundig Service" selected>', false);
    }

    public function test_service_booking_page_prepopulates_first_available_week(): void
    {
        $firstAvailableWeek = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        $response = $this->get(route('service.create'));

        $response->assertOk()
            ->assertSee('<option value="'.$firstAvailableWeek.'" selected>', false);
    }

    public function test_user_can_submit_service_request_for_grundig_service_and_other(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        $response = $this->post(route('service.store'), [
            'service_type' => Service::OTHER->value,
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'week_start' => $nextWeekStart,
            'message' => 'Trenger hjelp med bytte av felg og tilpassing av eiker.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas(ServiceRequest::class, [
            'email' => 'ola@example.com',
            'service_type' => Service::OTHER->value,
            'message' => 'Trenger hjelp med bytte av felg og tilpassing av eiker.',
        ]);
    }

    public function test_submitting_service_request_without_message_fails_validation(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        $this->post(route('service.store'), [
            'service_type' => Service::OTHER->value,
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'week_start' => $nextWeekStart,
            'message' => '',
        ])->assertSessionHasErrors('message');
    }

    public function test_user_can_submit_service_request_with_pickup_and_images(): void
    {
        Storage::fake('public');

        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');
        $file = UploadedFile::fake()->create('bike.jpg', 100, 'image/jpeg');

        $response = $this->post(route('service.store'), [
            'service_type' => Service::GRUNDIG_SERVICE->value,
            'name' => 'Kari Nordmann',
            'email' => 'kari@example.com',
            'phone' => '98765432',
            'week_start' => $nextWeekStart,
            'message' => 'Sykkelen trenger nye bremseklosser og girjustering.',
            'wants_pickup' => '1',
            'address' => 'Storgata 10, Bodø',
            'images' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $request = ServiceRequest::first();
        $this->assertNotNull($request);
        $this->assertSame(Service::GRUNDIG_SERVICE, $request->service_type);
        $this->assertSame('Kari Nordmann', $request->name);
        $this->assertSame('kari@example.com', $request->email);
        $this->assertTrue($request->wants_pickup);
        $this->assertSame('Storgata 10, Bodø', $request->address);
        $this->assertSame(ServiceStatus::PENDING, $request->status);
        $this->assertCount(1, $request->images);
        Storage::disk('public')->assertExists($request->images[0]);
    }

    public function test_invalid_service_type_fails_validation(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        $this->post(route('service.store'), [
            'service_type' => 'Ugyldig service',
            'name' => 'Kari Nordmann',
            'email' => 'kari@example.com',
            'week_start' => $nextWeekStart,
            'message' => 'Serviceønske',
        ])->assertSessionHasErrors('service_type');
    }

    public function test_available_weeks_excludes_approved_weeks(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        ServiceRequest::factory()->create([
            'week_start' => $nextWeekStart,
            'status' => ServiceStatus::APPROVED,
        ]);

        $this->get(route('service.create'))
            ->assertOk()
            ->assertDontSee('value="'.$nextWeekStart.'"', false);
    }

    public function test_available_weeks_excludes_unavailable_weeks(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        UnavailableWeek::create([
            'week_start' => $nextWeekStart,
            'reason' => 'Ferie',
        ]);

        $this->get(route('service.create'))
            ->assertOk()
            ->assertDontSee('value="'.$nextWeekStart.'"', false);
    }

    public function test_submitting_service_request_for_unavailable_week_fails_validation(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        UnavailableWeek::create([
            'week_start' => $nextWeekStart,
            'reason' => 'Stengt',
        ]);

        $this->post(route('service.store'), [
            'service_type' => Service::GRUNDIG_SERVICE->value,
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'week_start' => $nextWeekStart,
            'message' => 'Ønsker service i en sperret uke.',
        ])->assertSessionHasErrors('week_start');
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

    public function test_submitting_service_request_for_scheduled_week_fails_validation(): void
    {
        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');

        ServiceRequest::factory()->create([
            'week_start' => $nextWeekStart,
            'status' => ServiceStatus::APPROVED,
        ]);

        $this->post(route('service.store'), [
            'service_type' => Service::GRUNDIG_SERVICE->value,
            'name' => 'Ola Nordmann',
            'email' => 'ola@example.com',
            'week_start' => $nextWeekStart,
            'message' => 'Ønsker service i en uke som allerede er opptatt.',
        ])->assertSessionHasErrors('week_start');
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
