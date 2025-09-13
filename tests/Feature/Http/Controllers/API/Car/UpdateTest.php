<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController update method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create();

        $response = $this->putJson("/api/cars/{$car->id}", [
            'make' => 'Updated Make',
        ]);

        $response->assertUnauthorized();
    });

    it('updates car with valid data', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ]);

        $updateData = [
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'nickname' => 'Updated Car',
        ];

        $response = $this->putJson("/api/cars/{$car->id}", $updateData);

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

        expect($response->json('make'))->toBe('Honda');
        expect($response->json('model'))->toBe('Civic');
        expect($response->json('year'))->toBe(2021);
        expect($response->json('nickname'))->toBe('Updated Car');

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
        ]);
    });

    it('validates year is integer', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'year' => 'not-a-year',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['year']);
    });

    it('validates make is string when provided', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'make' => 123,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['make']);
    });

    it('validates model is string when provided', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'model' => 123,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['model']);
    });

    it('returns 404 for non-existent car', function () {
        $this->actingAs($this->user);

        $response = $this->putJson('/api/cars/999999', [
            'make' => 'Updated Make',
        ]);

        $response->assertNotFound();
    });

    it('allows partial updates', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'make' => 'Honda',
        ]);

        $response->assertSuccessful();
        expect($response->json('make'))->toBe('Honda');
        expect($response->json('model'))->toBe('Camry'); // unchanged
        expect($response->json('year'))->toBe(2020); // unchanged
    });
});
