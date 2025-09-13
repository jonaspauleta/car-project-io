<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

describe('Modification API Authorization', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->car = Car::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserCar = Car::factory()->create(['user_id' => $this->otherUser->id]);
        $this->modification = Modification::factory()->create(['car_id' => $this->car->id]);
        $this->otherUserModification = Modification::factory()->create(['car_id' => $this->otherUserCar->id]);
    });

    describe('GET /api/cars/{car}/modifications', function () {
        it('allows car owner to view modifications for their car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->car->id}/modifications");

            $response->assertSuccessful();
            $response->assertJsonCount(1, 'data');
            $response->assertJsonPath('data.0.id', $this->modification->id);
        });

        it('denies non-owner from viewing modifications for their car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->otherUserCar->id}/modifications");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->getJson("/api/cars/{$this->car->id}/modifications");

            $response->assertUnauthorized();
        });
    });

    describe('GET /api/cars/{car}/modifications/{modification}', function () {
        it('allows car owner to view modification for their car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}");

            $response->assertSuccessful();
            $response->assertJsonPath('id', $this->modification->id);
        });

        it('denies non-owner from viewing modification for their car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->otherUserCar->id}/modifications/{$this->otherUserModification->id}");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}");

            $response->assertUnauthorized();
        });
    });

    describe('POST /api/cars/{car}/modifications', function () {
        it('allows car owner to create modification for their car', function () {
            $modificationData = [
                'name' => 'Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Increases airflow',
                'cost' => 299.99,
            ];

            $response = $this->actingAs($this->user)
                ->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

            $response->assertCreated();
            $response->assertJsonPath('name', 'Cold Air Intake');
            $response->assertJsonPath('car_id', $this->car->id);
        });

        it('denies non-owner from creating modification for their car', function () {
            $modificationData = [
                'name' => 'Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Increases airflow',
                'cost' => 299.99,
            ];

            $response = $this->actingAs($this->user)
                ->postJson("/api/cars/{$this->otherUserCar->id}/modifications", $modificationData);

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $modificationData = [
                'name' => 'Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Increases airflow',
                'cost' => 299.99,
            ];

            $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

            $response->assertUnauthorized();
        });
    });

    describe('PUT /api/cars/{car}/modifications/{modification}', function () {
        it('allows car owner to update modification for their car', function () {
            $updateData = [
                'name' => 'Updated Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Updated notes',
                'cost' => 399.99,
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}", $updateData);

            $response->assertSuccessful();
            $response->assertJsonPath('name', 'Updated Cold Air Intake');
        });

        it('denies non-owner from updating modification for their car', function () {
            $updateData = [
                'name' => 'Updated Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Updated notes',
                'cost' => 399.99,
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/cars/{$this->otherUserCar->id}/modifications/{$this->otherUserModification->id}", $updateData);

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $updateData = [
                'name' => 'Updated Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Updated notes',
                'cost' => 399.99,
            ];

            $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}", $updateData);

            $response->assertUnauthorized();
        });
    });

    describe('DELETE /api/cars/{car}/modifications/{modification}', function () {
        it('allows car owner to delete modification for their car', function () {
            $response = $this->actingAs($this->user)
                ->deleteJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}");

            $response->assertNoContent();
            $this->assertDatabaseMissing('modifications', ['id' => $this->modification->id]);
        });

        it('denies non-owner from deleting modification for their car', function () {
            $response = $this->actingAs($this->user)
                ->deleteJson("/api/cars/{$this->otherUserCar->id}/modifications/{$this->otherUserModification->id}");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$this->modification->id}");

            $response->assertUnauthorized();
        });
    });
});
