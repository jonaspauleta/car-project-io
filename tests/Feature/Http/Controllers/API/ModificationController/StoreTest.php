<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
});

describe('ModificationController store method', function () {
    it('requires authentication', function () {
        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertUnauthorized();
    });

    it('creates a new modification with valid data', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'notes' => 'Increases airflow and performance',
            'brand' => 'K&N',
            'vendor' => 'AutoZone',
            'installation_date' => '2023-06-15T10:30:00Z',
            'cost' => 299.99,
            'is_active' => true,
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertCreated()
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

        expect($response->json('name'))->toBe('Cold Air Intake');
        expect($response->json('category'))->toBe('Engine');
        expect($response->json('cost'))->toBe(299.99);

        $this->assertDatabaseHas('modifications', [
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
        ]);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category']);
    });

    it('validates name is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 123,
            'category' => 'Engine',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates category is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 123,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    });

    it('validates cost is numeric and non-negative', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => -100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cost']);
    });

    it('validates installation_date is valid date', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'installation_date' => 'not-a-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['installation_date']);
    });

    it('validates is_active is boolean', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'is_active' => 'not-boolean',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['is_active']);
    });

    it('allows optional fields to be null', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'notes' => null,
            'brand' => null,
            'vendor' => null,
            'installation_date' => null,
            'cost' => null,
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertCreated();
        expect($response->json('notes'))->toBeNull();
        expect($response->json('brand'))->toBeNull();
        expect($response->json('vendor'))->toBeNull();
        expect($response->json('installation_date'))->toBeNull();
        expect($response->json('cost'))->toBeNull();
        expect($response->json('is_active'))->toBe(true); // default value
    });
});
