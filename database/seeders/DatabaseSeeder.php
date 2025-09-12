<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Car;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Car::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);
    }
}
