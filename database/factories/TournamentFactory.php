<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'location' => $this->faker->city,
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'type' => 'super_8_doubles',
            'min_players' => 8,
            'max_players' => 8,
            'status' => 'draft',
            'number_of_courts' => 2,
            'registration_code' => Str::random(8),
            'registration_open' => true,
        ];
    }
}
