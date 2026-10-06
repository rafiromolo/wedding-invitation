<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
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
            'template' => 'classic',
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'title' => 'Pernikahan ' . fake()->firstName() . ' & ' . fake()->firstName(),
            'event_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'event_data' => [
                'venue' => fake()->company(),
                'address' => fake()->address(),
            ],
            'expires_at' => null,
        ];
    }
}
