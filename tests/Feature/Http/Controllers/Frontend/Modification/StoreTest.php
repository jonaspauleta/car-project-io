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

describe('ModificationController store method', function () {
    it('requires authentication', function () {
        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
        ]);

        $response->assertRedirect('/login');
    });

    it('creates a new modification with valid data', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'notes' => 'High-performance exhaust system',
            'brand' => 'Borla',
            'vendor' => 'Amazon',
            'installation_date' => now()->subDays(5)->format('Y-m-d'),
            'cost' => 599.99,
            'is_active' => true,
        ];

        $response = $this->post("/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Modification created successfully.');

        $this->assertDatabaseHas('modifications', [
            'car_id' => $this->car->id,
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'notes' => 'High-performance exhaust system',
            'brand' => 'Borla',
            'vendor' => 'Amazon',
            'cost' => 599.99,
            'is_active' => true,
        ]);
    });

    it('creates a new modification with minimal required data', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
        ];

        $response = $this->post("/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Modification created successfully.');

        $this->assertDatabaseHas('modifications', [
            'car_id' => $this->car->id,
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'notes' => null,
            'brand' => null,
            'vendor' => null,
            'installation_date' => null,
            'cost' => null,
            'is_active' => true,
        ]);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user);

        $response = $this->post("/cars/{$this->car->id}/modifications", []);

        $response->assertSessionHasErrors(['name', 'category']);
    });

    it('validates name field', function () {
        $this->actingAs($this->user);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => '',
            'category' => 'Performance',
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    it('validates category field', function () {
        $this->actingAs($this->user);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => '',
        ]);

        $response->assertSessionHasErrors(['category']);
    });

    it('validates installation_date is not in future', function () {
        $this->actingAs($this->user);

        $futureDate = now()->addDay()->format('Y-m-d');

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'installation_date' => $futureDate,
        ]);

        $response->assertSessionHasErrors(['installation_date']);
    });

    it('validates cost is not negative', function () {
        $this->actingAs($this->user);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'cost' => -100,
        ]);

        $response->assertSessionHasErrors(['cost']);
    });

    it('validates cost does not exceed maximum', function () {
        $this->actingAs($this->user);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'cost' => 1000000,
        ]);

        $response->assertSessionHasErrors(['cost']);
    });

    it('validates notes length', function () {
        $this->actingAs($this->user);

        $longNotes = str_repeat('a', 1001);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
            'notes' => $longNotes,
        ]);

        $response->assertSessionHasErrors(['notes']);
    });

    it('forbids creating modifications for other users cars', function () {
        $this->actingAs($this->otherUser);

        $response = $this->post("/cars/{$this->car->id}/modifications", [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
        ]);

        $response->assertForbidden();
    });

    it('redirects to modification show page after creation', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Performance Exhaust',
            'category' => 'Performance',
        ];

        $response = $this->post("/cars/{$this->car->id}/modifications", $modificationData);

        $modification = Modification::where('car_id', $this->car->id)->first();
        $response->assertRedirect(route('cars.modifications.show', [$this->car, $modification]));
    });
});
