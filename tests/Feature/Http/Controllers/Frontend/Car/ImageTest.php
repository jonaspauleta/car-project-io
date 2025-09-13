<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    Storage::fake('private');
});

describe('CarController image method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertRedirect('/login');
    });

    it('serves car image for owner', function () {
        $this->actingAs($this->user);

        // Create a fake image file
        $imageFile = UploadedFile::fake()->image('car.jpg', 800, 600);
        $imagePath = $imageFile->store('cars', 'private');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => $imagePath,
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Cache-Control', 'max-age=3600, private');
    });

    it('returns 404 for car without image', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => null,
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertNotFound();
    });

    it('returns 404 for non-existent image file', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => 'cars/non-existent.jpg',
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertNotFound();
    });

    it('forbids access to other users car images', function () {
        $this->actingAs($this->otherUser);

        // Create a fake image file
        $imageFile = UploadedFile::fake()->image('car.jpg', 800, 600);
        $imagePath = $imageFile->store('cars', 'private');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => $imagePath,
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertForbidden();
    });

    it('serves different image types with correct content type', function () {
        $this->actingAs($this->user);

        // Test PNG image
        $pngFile = UploadedFile::fake()->image('car.png', 800, 600);
        $pngPath = $pngFile->store('cars', 'private');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => $pngPath,
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'image/png');
    });

    it('handles external image URLs', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => 'https://example.com/car.jpg',
        ]);

        $response = $this->get("/cars/{$car->id}/image");

        // External URLs should return 404 since we can't serve them through this endpoint
        $response->assertNotFound();
    });
});
