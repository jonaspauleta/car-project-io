<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController show method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}");

        $response->assertRedirect('/login');
    });

    it('shows car details for owner', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        Modification::factory()->count(2)->create(['car_id' => $car->id]);

        $response = $this->get("/cars/{$car->id}");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Show')
                ->has('car')
                ->where('car.id', $car->id)
                ->has('car.modifications', 2)
            );
    });

    it('forbids access to other users cars', function () {
        $this->actingAs($this->otherUser);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}");

        $response->assertForbidden();
    });

    it('loads modifications ordered by installation date desc', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $modification1 = Modification::factory()->create([
            'car_id' => $car->id,
            'installation_date' => now()->subDays(2)
        ]);
        $modification2 = Modification::factory()->create([
            'car_id' => $car->id,
            'installation_date' => now()->subDay()
        ]);

        $response = $this->get("/cars/{$car->id}");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Show')
                ->where('car.modifications.0.id', $modification2->id)
                ->where('car.modifications.1.id', $modification1->id)
            );
    });
});
