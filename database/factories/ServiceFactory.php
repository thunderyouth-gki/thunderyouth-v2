<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_date' => $this->faker->date(),
            'service_type' => 'Youth',
            'theme' => $this->faker->sentence(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'place' => 'Main Hall',
        ];
    }
}
