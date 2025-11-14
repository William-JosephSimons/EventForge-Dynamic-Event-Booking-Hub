<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            // Ensure the event date is in the future
            'date_time' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'location' => "{$this->faker->city}, {$this->faker->state}",
            'capacity' => $this->faker->numberBetween(50, 200),
        ];
    }
}
