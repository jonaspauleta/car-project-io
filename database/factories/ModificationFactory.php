<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Car;
use App\Models\Modification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Modification>
 */
class ModificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Modification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'name' => fake()->word(),
            'category' => fake()->word(),
            'notes' => fake()->sentence(),
            'brand' => fake()->word(),
            'vendor' => fake()->word(),
            'installation_date' => fake()->dateTime(),
            'cost' => fake()->randomFloat(2, 0, 10000),
            'is_active' => fake()->boolean(),
        ];
    }
}
