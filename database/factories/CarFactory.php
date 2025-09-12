<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'make' => fake()->word(),
            'model' => fake()->word(),
            'year' => fake()->year(),
            'nickname' => fake()->word(),
            'vin' => fake()->numerify('#################'),
            'image_url' => fake()->imageUrl(),
            'notes' => fake()->sentence(),
        ];
    }
}
