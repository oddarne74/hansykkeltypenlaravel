<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use App\Mail\ServiceStatusUpdated;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\ServiceRequestSeeder;
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
            ->assertSee('Én sykkel per uke')
            ->assertSee('Sykkelsjekk')
            ->assertSee('349 kr')
            ->assertSee('Full service')
            ->assertSee('999 kr');
    }

    public function test_user_can_submit_service_request_with_pickup_and_images(): void
    {
        Storage::fake('public');

        $nextWeekStart = CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d');
        $file = UploadedFile::fake()->create('bike.jpg', 100, 'image/jpeg');

        $response = $this->post(route('service.store'), [
            'service_type' => ServiceType::FULL_SERVICE->value,
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
        $this->assertSame(ServiceType::FULL_SERVICE, $request->service_type);
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
}
