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
        $eventTypes = [
            'Track Day Experience',
            'Open Track Day',
            'Racing School Event',
            'Time Attack Series',
            'Driver Training Day',
            'Supercar Track Day',
            'Beginner Track Day',
            'Advanced Driving Course',
            'Car Club Track Day',
            'Performance Driving Experience'
        ];

        $startDate = $this->faker->dateTimeBetween('now', '+6 months');

        return [
            'track_id' => Track::factory(),
            'organizer_id' => Organizer::factory(),
            'title' => $this->faker->randomElement($eventTypes),
            'description' => $this->faker->sentence(),
            'start_date' => $startDate,
            'end_date' => $startDate,
            'website' => $this->faker->url(),
        ];
    }
}
