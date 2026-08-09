<?php

namespace Tests\Feature;

use App\Enums\BikeStatus;
use App\Mail\BikeAvailable;
use App\Models\Bike;
use App\Models\BikeInterest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BikeInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_interest_for_reserved_bike(): void
    {
        $bike = Bike::factory()->create(['status' => BikeStatus::RESERVED, 'published_at' => now()]);

        $this->post(route('bikes.interest.store', $bike->slug), ['email' => 'test@example.com'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bike_interests', [
            'bike_id' => $bike->id,
            'email' => 'test@example.com',
        ]);
    }

    public function test_interest_registration_sends_email_when_status_changes_to_for_sale(): void
    {
        Mail::fake();

        $bike = Bike::factory()->create(['status' => BikeStatus::RESERVED, 'published_at' => now()]);
        BikeInterest::create(['bike_id' => $bike->id, 'email' => 'test@example.com']);

        $this->assertDatabaseHas('bike_interests', [
            'bike_id' => $bike->id,
            'email' => 'test@example.com',
        ]);

        $bike->update(['status' => BikeStatus::FOR_SALE]);

        Mail::assertQueued(BikeAvailable::class, function ($mail) use ($bike) {
            return $mail->bike->id === $bike->id;
        });

        $this->assertDatabaseMissing('bike_interests', [
            'bike_id' => $bike->id,
            'email' => 'test@example.com',
        ]);
    }
}
