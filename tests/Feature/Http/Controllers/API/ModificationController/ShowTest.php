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

describe('ModificationController show method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertUnauthorized();
    });

    it('returns modification details for authenticated user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'car_id',
                'name',
                'category',
                'notes',
                'brand',
                'vendor',
                'installation_date',
                'cost',
                'is_active',
            ]);

        expect($response->json('id'))->toBe($modification->id);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/999999");

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNotFound();
    });

    it('includes car relationship when requested', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}?include[]=car");

        $response->assertSuccessful();
        expect($response->json('car'))->not->toBeNull();
        expect($response->json('car.id'))->toBe($this->car->id);
    });
});
