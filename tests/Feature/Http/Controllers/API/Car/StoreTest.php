<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController store method', function () {
    it('requires authentication', function () {
        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ];

        $response = $this->postJson('/api/cars', $carData);

        $response->assertUnauthorized();
    });

    it('creates a new car with valid data', function () {
        $this->actingAs($this->user);

        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'nickname' => 'My Daily Driver',
            'vin' => '1HGBH41JXMN109186',
            'notes' => 'Great condition',
        ];

        $response = $this->postJson('/api/cars', $carData);

        $response->assertCreated()
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

        expect($response->json('make'))->toBe('Toyota');
        expect($response->json('model'))->toBe('Camry');
        expect($response->json('year'))->toBe(2020);

        $this->assertDatabaseHas('cars', [
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ]);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/cars', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['make', 'model', 'year']);
    });

    it('validates make is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/cars', [
            'make' => 123,
            'model' => 'Camry',
            'year' => 2020,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['make']);
    });

    it('validates model is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/cars', [
            'make' => 'Toyota',
            'model' => 123,
            'year' => 2020,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['model']);
    });

    it('validates year is integer', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 'not-a-year',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['year']);
    });

    it('allows optional fields to be null', function () {
        $this->actingAs($this->user);

        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'nickname' => null,
            'vin' => null,
            'notes' => null,
        ];

        $response = $this->postJson('/api/cars', $carData);

        $response->assertCreated();
        expect($response->json('nickname'))->toBeNull();
        expect($response->json('vin'))->toBeNull();
        expect($response->json('notes'))->toBeNull();
    });
});
