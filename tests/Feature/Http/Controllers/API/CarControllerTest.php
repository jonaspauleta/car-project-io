<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('index method', function () {
    it('requires authentication', function () {
        $response = $this->getJson('/api/cars');

        $response->assertUnauthorized();
    });

    it('returns paginated list of cars for authenticated user', function () {
        $this->actingAs($this->user);

        Car::factory()->count(3)->create(['user_id' => $this->user->id]);
        Car::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson('/api/cars');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'make',
                        'model',
                        'year',
                        'nickname',
                        'vin',
                        'image_url',
                        'notes',
                    ],
                ],
                'links',
                'meta',
            ]);

        expect($response->json('data'))->toHaveCount(3);
    });

    it('supports pagination', function () {
        $this->actingAs($this->user);

        Car::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/cars?page=2&per_page=5');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(5);
        expect($response->json('meta.current_page'))->toBe(2);
    });

    it('supports filtering by make', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'make' => 'Toyota']);
        Car::factory()->create(['user_id' => $this->user->id, 'make' => 'Honda']);

        $response = $this->getJson('/api/cars?filter[make]=Toyota');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.make'))->toBe('Toyota');
    });

    it('supports filtering by model', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'model' => 'Camry']);
        Car::factory()->create(['user_id' => $this->user->id, 'model' => 'Civic']);

        $response = $this->getJson('/api/cars?filter[model]=Camry');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.model'))->toBe('Camry');
    });

    it('supports filtering by year', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'year' => 2020]);
        Car::factory()->create(['user_id' => $this->user->id, 'year' => 2021]);

        $response = $this->getJson('/api/cars?filter[year]=2020');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.year'))->toBe(2020);
    });

    it('supports filtering by nickname', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'nickname' => 'My Car']);
        Car::factory()->create(['user_id' => $this->user->id, 'nickname' => 'Other Car']);

        $response = $this->getJson('/api/cars?filter[nickname]=My Car');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.nickname'))->toBe('My Car');
    });

    it('supports filtering by VIN', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'vin' => 'VIN123456789']);
        Car::factory()->create(['user_id' => $this->user->id, 'vin' => 'VIN987654321']);

        $response = $this->getJson('/api/cars?filter[vin]=VIN123456789');

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.vin'))->toBe('VIN123456789');
    });

    it('supports sorting by id', function () {
        $this->actingAs($this->user);

        $car1 = Car::factory()->create(['user_id' => $this->user->id]);
        $car2 = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/cars?sort=id');

        $response->assertSuccessful();
        expect($response->json('data.0.id'))->toBe($car1->id);
        expect($response->json('data.1.id'))->toBe($car2->id);
    });

    it('supports sorting by make', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'make' => 'Zebra']);
        Car::factory()->create(['user_id' => $this->user->id, 'make' => 'Apple']);

        $response = $this->getJson('/api/cars?sort=make');

        $response->assertSuccessful();
        expect($response->json('data.0.make'))->toBe('Apple');
        expect($response->json('data.1.make'))->toBe('Zebra');
    });

    it('supports sorting by year', function () {
        $this->actingAs($this->user);

        Car::factory()->create(['user_id' => $this->user->id, 'year' => 2022]);
        Car::factory()->create(['user_id' => $this->user->id, 'year' => 2020]);

        $response = $this->getJson('/api/cars?sort=year');

        $response->assertSuccessful();
        expect($response->json('data.0.year'))->toBe(2020);
        expect($response->json('data.1.year'))->toBe(2022);
    });

    it('includes user relationship when requested', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/cars?include[]=user');

        $response->assertSuccessful();
        expect($response->json('data.0.user'))->not->toBeNull();
        expect($response->json('data.0.user.id'))->toBe($this->user->id);
    });

    it('includes modifications relationship when requested', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        Modification::factory()->count(2)->create(['car_id' => $car->id]);

        $response = $this->getJson('/api/cars?include[]=modifications');

        $response->assertSuccessful();
        expect($response->json('data.0.modifications'))->not->toBeNull();
        expect($response->json('data.0.modifications'))->toHaveCount(2);
    });

    it('includes multiple relationships when requested', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        Modification::factory()->create(['car_id' => $car->id]);

        $response = $this->getJson('/api/cars?include[]=user&include[]=modifications');

        $response->assertSuccessful();
        expect($response->json('data.0.user'))->not->toBeNull();
        expect($response->json('data.0.modifications'))->not->toBeNull();
    });
});

describe('show method', function () {
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

describe('store method', function () {
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

describe('update method', function () {
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

describe('destroy method', function () {
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
