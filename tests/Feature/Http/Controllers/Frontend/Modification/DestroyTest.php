<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
});

describe('ModificationController destroy method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->delete("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertRedirect('/login');
    });

    it('deletes modification for car owner', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->delete("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertRedirect(route('cars.modifications.index', $this->car));
        $response->assertSessionHas('success', 'Modification deleted successfully.');

        $this->assertDatabaseMissing('modifications', ['id' => $modification->id]);
    });

    it('forbids deleting other users modifications', function () {
        $this->actingAs($this->otherUser);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->delete("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('modifications', ['id' => $modification->id]);
    });
});
