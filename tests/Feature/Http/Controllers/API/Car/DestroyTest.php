<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController destroy method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create();

        $response = $this->deleteJson("/api/cars/{$car->id}");

        $response->assertUnauthorized();
    });

    it('deletes car successfully', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/cars/{$car->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id,
        ]);
    });

    it('returns 404 for non-existent car', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/cars/999999');

        $response->assertNotFound();
    });
});
