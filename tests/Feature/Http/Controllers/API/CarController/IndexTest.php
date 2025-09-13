<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController index method', function () {
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
