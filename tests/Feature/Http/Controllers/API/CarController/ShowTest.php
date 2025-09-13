<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController show method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create();

        $response = $this->getJson("/api/cars/{$car->id}");

        $response->assertUnauthorized();
    });

    it('returns car details for authenticated user', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/cars/{$car->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'make',
                'model',
                'year',
                'nickname',
                'vin',
                'image_url',
                'notes',
            ]);

        expect($response->json('id'))->toBe($car->id);
    });

    it('returns 404 for non-existent car', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/cars/999999');

        $response->assertNotFound();
    });

    it('includes user relationship when requested', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/cars/{$car->id}?include[]=user");

        $response->assertSuccessful();
        expect($response->json('user'))->not->toBeNull();
        expect($response->json('user.id'))->toBe($this->user->id);
    });

    it('includes modifications relationship when requested', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        Modification::factory()->count(3)->create(['car_id' => $car->id]);

        $response = $this->getJson("/api/cars/{$car->id}?include[]=modifications");

        $response->assertSuccessful();
        expect($response->json('modifications'))->not->toBeNull();
        expect($response->json('modifications'))->toHaveCount(3);
    });
});
