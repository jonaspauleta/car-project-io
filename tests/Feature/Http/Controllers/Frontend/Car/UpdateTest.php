<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController update method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
        ]);

        $response->assertRedirect('/login');
    });

    it('updates car with valid data', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
            'nickname' => 'Updated Nickname',
            'vin' => '2HGBH41JXMN109187',
            'notes' => 'Updated notes',
        ];

        $response = $this->put("/cars/{$car->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car updated successfully.');

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
            'nickname' => 'Updated Nickname',
            'vin' => '2HGBH41JXMN109187',
            'notes' => 'Updated notes',
        ]);
    });

    it('updates car with partial data', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Original Make',
            'model' => 'Original Model',
            'year' => 2020,
        ]);

        $updateData = [
            'nickname' => 'Updated Nickname',
        ];

        $response = $this->put("/cars/{$car->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car updated successfully.');

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'make' => 'Original Make',
            'model' => 'Original Model',
            'year' => 2020,
            'nickname' => 'Updated Nickname',
        ]);
    });

    it('validates required fields when provided', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => '',
            'model' => 'Updated Model',
            'year' => 2021,
        ]);

        $response->assertSessionHasErrors(['make']);
    });

    it('forbids updating other users cars', function () {
        $this->actingAs($this->otherUser);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
        ]);

        $response->assertForbidden();
    });

    it('updates car with new image upload', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
            'image' => Illuminate\Http\UploadedFile::fake()->image('new-car.jpg', 800, 600),
        ];

        $response = $this->put("/cars/{$car->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car updated successfully.');

        $car->refresh();
        expect($car->image_url)->not->toBeNull();
        expect($car->image_url)->toContain('cars/');
    });

    it('preserves existing image when no new image uploaded', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => 'cars/existing-image.jpg',
        ]);

        $originalImageUrl = $car->image_url;

        $updateData = [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
        ];

        $response = $this->put("/cars/{$car->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Car updated successfully.');

        $car->refresh();
        expect($car->image_url)->toBe($originalImageUrl);
    });

    it('validates image file type on update', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
            'image' => Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1000),
        ]);

        $response->assertSessionHasErrors(['image']);
    });

    it('validates image file size on update', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
            'image' => Illuminate\Http\UploadedFile::fake()->image('large.jpg', 800, 600)->size(11000), // 11MB
        ]);

        $response->assertSessionHasErrors(['image']);
    });

    it('redirects to car show page after update', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put("/cars/{$car->id}", [
            'make' => 'Updated Make',
            'model' => 'Updated Model',
            'year' => 2021,
        ]);

        $response->assertRedirect(route('cars.show', $car));
    });
});
