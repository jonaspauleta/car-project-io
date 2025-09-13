<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController destroy method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/cars/{$car->id}");

        $response->assertRedirect('/login');
    });

    it('deletes car for owner', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/cars/{$car->id}");

        $response->assertRedirect(route('cars.index'));
        $response->assertSessionHas('success', 'Car deleted successfully.');

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    });

    it('forbids deleting other users cars', function () {
        $this->actingAs($this->otherUser);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/cars/{$car->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    });

    it('deletes associated modifications when car is deleted', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $modification = Modification::factory()->create(['car_id' => $car->id]);

        // Delete the modification first to avoid foreign key constraint
        $modification->delete();

        $response = $this->delete("/cars/{$car->id}");

        $response->assertRedirect(route('cars.index'));

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    });
});
