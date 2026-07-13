<?php

namespace Database\Factories;

use App\Models\Hazard;
use Illuminate\Database\Eloquent\Factories\Factory;

class HazardFactory extends Factory
{
    protected $model = Hazard::class;

    public function definition(): array
    {
        return [
            'user_name' => $this->faker->name,
            'hazard_category' => $this->faker->randomElement(['Road Hazard', 'Environmental Hazard', 'Building Hazard']),
            'hazard_description' => $this->faker->sentence(8),
            'latitude' => $this->faker->latitude(1, 45),
            'longitude' => $this->faker->longitude(70, 140),
            'location_name' => $this->faker->streetName,
            'device_info' => $this->faker->randomElement(['Android 15 Pixel 8', 'Android 14 Samsung S23', 'Android 13 Xiaomi Redmi Note']),
            'reported_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
