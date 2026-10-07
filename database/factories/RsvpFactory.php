<?php

namespace Database\Factories;

use App\Models\Rsvp;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rsvp>
 */
class RsvpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['attending', 'declined', 'maybe']);
        return [
            'guest_id' => Guest::factory(),
            'status' => $status,
            'attending_count' => $status === 'attending' ? fake()->numberBetween(1, 3) : 0,
            'message' => fake()->optional(0.6)->sentence(),
        ];
    }
}
