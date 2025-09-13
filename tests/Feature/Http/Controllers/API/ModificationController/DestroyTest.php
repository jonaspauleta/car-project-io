<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
    $this->otherCar = Car::factory()->create(['user_id' => $this->otherUser->id]);
});

describe('ModificationController destroy method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertUnauthorized();
    });

    it('deletes modification successfully', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('modifications', [
            'id' => $modification->id,
        ]);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/999999");

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNotFound();
    });
});
