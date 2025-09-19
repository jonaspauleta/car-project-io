<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organizerNames = [
            'Track Day Events Ltd',
            'Racing Experiences Inc',
            'Performance Driving Academy',
            'Supercar Track Days',
            'Circuit Masters',
            'Apex Track Events',
            'Velocity Track Days',
            'Elite Driving Experiences',
            'Track Time Events',
            'Racing School International',
            'Fast Lane Track Days',
            'Circuit Experience Co',
            'Track Masters Events'
        ];

        return [
            'name' => $this->faker->randomElement($organizerNames),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'logo_url' => $this->faker->imageUrl(200, 200, 'business'),
        ];
    }
}
