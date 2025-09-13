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

describe('ModificationController update method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertUnauthorized();
    });

    it('updates modification with valid data', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => 299.99,
        ]);

        $updateData = [
            'name' => 'High Flow Air Intake',
            'category' => 'Performance',
            'cost' => 399.99,
            'brand' => 'K&N',
        ];

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", $updateData);

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

        expect($response->json('name'))->toBe('High Flow Air Intake');
        expect($response->json('category'))->toBe('Performance');
        expect($response->json('cost'))->toBe(399.99);
        expect($response->json('brand'))->toBe('K&N');

        $this->assertDatabaseHas('modifications', [
            'id' => $modification->id,
            'name' => 'High Flow Air Intake',
            'category' => 'Performance',
            'cost' => 399.99,
        ]);
    });

    it('validates car_id exists when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'car_id' => 999999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['car_id']);
    });

    it('validates cost is numeric and non-negative when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'cost' => -100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cost']);
    });

    it('validates installation_date is valid date when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'installation_date' => 'not-a-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['installation_date']);
    });

    it('validates is_active is boolean when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'is_active' => 'not-boolean',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['is_active']);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/999999", [
            'name' => 'Updated Name',
        ]);

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertNotFound();
    });

    it('allows partial updates', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => 299.99,
        ]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'High Flow Air Intake',
        ]);

        $response->assertSuccessful();
        expect($response->json('name'))->toBe('High Flow Air Intake');
        expect($response->json('category'))->toBe('Engine'); // unchanged
        expect($response->json('cost'))->toBe(299.99); // unchanged
    });
});
