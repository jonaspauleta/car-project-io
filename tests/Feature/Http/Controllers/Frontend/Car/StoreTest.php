<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController store method', function () {
    it('requires authentication', function () {
        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ]);

        $response->assertRedirect('/login');
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

        $response = $this->post('/cars', $carData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car created successfully.');

        $this->assertDatabaseHas('cars', [
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'nickname' => 'My Daily Driver',
            'vin' => '1HGBH41JXMN109186',
            'notes' => 'Great condition',
        ]);
    });

    it('creates a new car with image upload', function () {
        $this->actingAs($this->user);

        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'image' => Illuminate\Http\UploadedFile::fake()->image('car.jpg', 800, 600),
        ];

        $response = $this->post('/cars', $carData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car created successfully.');

        $car = Car::where('user_id', $this->user->id)->first();
        expect($car->image_url)->not->toBeNull();
        expect($car->image_url)->toContain('cars/');
    });

    it('creates a new car with minimal required data', function () {
        $this->actingAs($this->user);

        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ];

        $response = $this->post('/cars', $carData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car created successfully.');

        $this->assertDatabaseHas('cars', [
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'nickname' => null,
            'vin' => null,
            'notes' => null,
        ]);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', []);

        $response->assertSessionHasErrors(['make', 'model', 'year']);
    });

    it('validates make field', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => '',
            'model' => 'Camry',
            'year' => 2020,
        ]);

        $response->assertSessionHasErrors(['make']);
    });

    it('validates model field', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => '',
            'year' => 2020,
        ]);

        $response->assertSessionHasErrors(['model']);
    });

    it('validates year field', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => '',
        ]);

        $response->assertSessionHasErrors(['year']);
    });

    it('validates year is not in future', function () {
        $this->actingAs($this->user);

        $futureYear = (int) date('Y') + 2;

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => $futureYear,
        ]);

        $response->assertSessionHasErrors(['year']);
    });

    it('validates year is not too old', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 1800,
        ]);

        $response->assertSessionHasErrors(['year']);
    });

    it('validates VIN length', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'vin' => 'INVALID',
        ]);

        $response->assertSessionHasErrors(['vin']);
    });

    it('validates notes length', function () {
        $this->actingAs($this->user);

        $longNotes = str_repeat('a', 1001);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'notes' => $longNotes,
        ]);

        $response->assertSessionHasErrors(['notes']);
    });

    it('validates image file type', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'image' => Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1000),
        ]);

        $response->assertSessionHasErrors(['image']);
    });

    it('validates image file size', function () {
        $this->actingAs($this->user);

        // Create a fake image that's larger than 10MB
        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'image' => Illuminate\Http\UploadedFile::fake()->image('large.jpg', 800, 600)->size(11000), // 11MB
        ]);

        $response->assertSessionHasErrors(['image']);
    });

    it('accepts valid image files', function () {
        $this->actingAs($this->user);

        $response = $this->post('/cars', [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
            'image' => Illuminate\Http\UploadedFile::fake()->image('car.jpg', 800, 600)->size(5000), // 5MB
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car created successfully.');
    });

    it('redirects to car show page after creation', function () {
        $this->actingAs($this->user);

        $carData = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2020,
        ];

        $response = $this->post('/cars', $carData);

        $car = Car::where('user_id', $this->user->id)->first();
        $response->assertRedirect(route('cars.show', $car));
    });
});
