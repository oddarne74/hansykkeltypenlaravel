<?php

namespace Database\Factories;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Models\ServiceRequest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'service_type' => $this->faker->randomElement(ServiceType::cases()),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->paragraph(),
            'week_start' => CarbonImmutable::now()->startOfWeek()->addWeek()->format('Y-m-d'),
            'wants_pickup' => false,
            'address' => null,
            'status' => ServiceStatus::PENDING,
            'images' => null,
        ];
    }
}
