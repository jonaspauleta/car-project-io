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

describe('ModificationController update method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
            'category' => 'Updated Category',
        ]);

        $response->assertRedirect('/login');
    });

    it('updates modification with valid data', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $updateData = [
            'name' => 'Updated Name',
            'category' => 'Updated Category',
            'notes' => 'Updated notes',
            'brand' => 'Updated Brand',
            'vendor' => 'Updated Vendor',
            'installation_date' => now()->subDays(3)->format('Y-m-d'),
            'cost' => 699.99,
            'is_active' => false,
        ];

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Modification updated successfully.');

        $this->assertDatabaseHas('modifications', [
            'id' => $modification->id,
            'name' => 'Updated Name',
            'category' => 'Updated Category',
            'notes' => 'Updated notes',
            'brand' => 'Updated Brand',
            'vendor' => 'Updated Vendor',
            'cost' => 699.99,
            'is_active' => false,
        ]);
    });

    it('updates modification with partial data', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Original Name',
            'category' => 'Original Category',
            'cost' => 500.00,
        ]);

        $updateData = [
            'cost' => 750.00,
        ];

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Modification updated successfully.');

        $this->assertDatabaseHas('modifications', [
            'id' => $modification->id,
            'name' => 'Original Name',
            'category' => 'Original Category',
            'cost' => 750.00,
        ]);
    });

    it('validates required fields when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => '',
            'category' => 'Updated Category',
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    it('forbids updating other users modifications', function () {
        $this->actingAs($this->otherUser);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
            'category' => 'Updated Category',
        ]);

        $response->assertForbidden();
    });

    it('redirects to modification show page after update', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->put("/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
            'category' => 'Updated Category',
        ]);

        $response->assertRedirect(route('cars.modifications.show', [$this->car, $modification]));
    });
});
