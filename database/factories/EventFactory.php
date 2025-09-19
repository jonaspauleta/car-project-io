<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organizer;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'track_id' => Track::factory(),
            'organizer_id' => Organizer::factory(),
            'title' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'website' => $this->faker->url(),
        ];
    }
}
