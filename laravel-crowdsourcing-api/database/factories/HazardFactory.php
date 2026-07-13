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
            'username' => $this->faker->userName,
            'category' => $this->faker->randomElement(['Road Hazard', 'Environmental Hazard', 'Building Hazard']),
            'description' => $this->faker->sentence(8),
            'latitude' => $this->faker->latitude(1, 45),
            'longitude' => $this->faker->longitude(70, 140),
            'device_info' => $this->faker->randomElement(['Android 15', 'iOS 18', 'Samsung Galaxy S24']),
            'reported_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
