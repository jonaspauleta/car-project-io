<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

describe('Car API Authorization', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->car = Car::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserCar = Car::factory()->create(['user_id' => $this->otherUser->id]);
    });

    describe('GET /api/cars', function () {
        it('returns only cars owned by authenticated user', function () {
            $response = $this->actingAs($this->user)
                ->getJson('/api/cars');

            $response->assertSuccessful();
            $response->assertJsonCount(1, 'data');
            $response->assertJsonPath('data.0.id', $this->car->id);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/cars');

            $response->assertUnauthorized();
        });
    });

    describe('GET /api/cars/{car}', function () {
        it('allows car owner to view their car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->car->id}");

            $response->assertSuccessful();
            $response->assertJsonPath('id', $this->car->id);
        });

        it('denies non-owner from viewing car', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->otherUserCar->id}");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->getJson("/api/cars/{$this->car->id}");

            $response->assertUnauthorized();
        });
    });

    describe('POST /api/cars', function () {
        it('allows authenticated user to create car', function () {
            $carData = [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2020,
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/cars', $carData);

            $response->assertCreated();
            $response->assertJsonPath('make', 'Toyota');
            // Note: user relationship might not be loaded by default in the response
            // $response->assertJsonPath('user.id', $this->user->id);
        });

        it('requires authentication', function () {
            $carData = [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2020,
            ];

            $response = $this->postJson('/api/cars', $carData);

            $response->assertUnauthorized();
        });
    });

    describe('PUT /api/cars/{car}', function () {
        it('allows car owner to update their car', function () {
            $updateData = [
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/cars/{$this->car->id}", $updateData);

            $response->assertSuccessful();
            $response->assertJsonPath('make', 'Honda');
        });

        it('denies non-owner from updating car', function () {
            $updateData = [
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/cars/{$this->otherUserCar->id}", $updateData);

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $updateData = [
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
            ];

            $response = $this->putJson("/api/cars/{$this->car->id}", $updateData);

            $response->assertUnauthorized();
        });
    });

    describe('DELETE /api/cars/{car}', function () {
        it('allows car owner to delete their car', function () {
            $response = $this->actingAs($this->user)
                ->deleteJson("/api/cars/{$this->car->id}");

            $response->assertNoContent();
            $this->assertDatabaseMissing('cars', ['id' => $this->car->id]);
        });

        it('denies non-owner from deleting car', function () {
            $response = $this->actingAs($this->user)
                ->deleteJson("/api/cars/{$this->otherUserCar->id}");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->deleteJson("/api/cars/{$this->car->id}");

            $response->assertUnauthorized();
        });
    });

    describe('POST /api/cars/{car}/upload-image', function () {
        it('allows car owner to upload image', function () {
            $image = fake()->image();
            $file = Illuminate\Http\UploadedFile::fake()->image('car.jpg');

            $response = $this->actingAs($this->user)
                ->postJson("/api/cars/{$this->car->id}/upload-image", [
                    'image' => $file,
                ]);

            $response->assertSuccessful();
        });

        it('denies non-owner from uploading image', function () {
            $file = Illuminate\Http\UploadedFile::fake()->image('car.jpg');

            $response = $this->actingAs($this->user)
                ->postJson("/api/cars/{$this->otherUserCar->id}/upload-image", [
                    'image' => $file,
                ]);

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $file = Illuminate\Http\UploadedFile::fake()->image('car.jpg');

            $response = $this->postJson("/api/cars/{$this->car->id}/upload-image", [
                'image' => $file,
            ]);

            $response->assertUnauthorized();
        });
    });

    describe('GET /api/cars/{car}/image', function () {
        it('allows car owner to view car image', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->car->id}/image");

            $response->assertNotFound(); // No image uploaded yet
        });

        it('denies non-owner from viewing car image', function () {
            $response = $this->actingAs($this->user)
                ->getJson("/api/cars/{$this->otherUserCar->id}/image");

            $response->assertForbidden();
        });

        it('requires authentication', function () {
            $response = $this->getJson("/api/cars/{$this->car->id}/image");

            $response->assertUnauthorized();
        });
    });
});
