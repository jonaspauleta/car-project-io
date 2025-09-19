<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);

        Modification::factory()->count(10)->create([
            'car_id' => $car->id,
        ]);

        $car = Car::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        Event::factory(10)->create();
    }
}
