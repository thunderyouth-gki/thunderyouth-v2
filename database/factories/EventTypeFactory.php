<?php

namespace Database\Factories;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventType>
 */
class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Service (Kebaktian/Ibadah)',
        ];
    }
}
