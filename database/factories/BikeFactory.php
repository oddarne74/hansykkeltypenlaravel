<?php

namespace Database\Factories;

use App\Models\Bike;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bike>
 */
class BikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'brand' => $this->faker->word(),
            'model' => $this->faker->word(),
            'type' => \App\Enums\BikeType::Hybridsykkel->value,
            'price' => $this->faker->numberBetween(1000, 10000),
            'status' => \App\Enums\BikeStatus::FOR_SALE->value,
            'size' => \App\Enums\BikeSize::M->value,
            'rider_height' => '170-180 cm',
            'wheel_size' => '28"',
            'gears' => 'Shimano 3x8',
            'frame' => 'Aluminium',
            'brakes' => 'Skivebremser',
            'color' => 'Sort',
            'year' => '2023',
            'description' => $this->faker->paragraph(),
            'condition_notes' => $this->faker->sentence(),
            'featured' => false,
            'published_at' => now(),
        ];
    }
}
