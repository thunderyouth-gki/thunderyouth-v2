<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_date' => $this->faker->date(),
            'event_type_id' => EventType::firstOrCreate(
                ['id' => 1],
                ['name' => 'Service (Kebaktian/Ibadah)']
            )->id,
            'theme' => $this->faker->sentence(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'place' => 'Main Hall',
        ];
    }
}
