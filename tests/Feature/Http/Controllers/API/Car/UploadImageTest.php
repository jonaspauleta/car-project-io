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

describe('CarController uploadImage method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create();
        $image = UploadedFile::fake()->image('car.jpg');

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $image,
        ]);

        $response->assertUnauthorized();
    });

    it('successfully uploads an image for owned car', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $image = UploadedFile::fake()->image('car.jpg', 800, 600);

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $image,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'make',
                'model',
                'year',
                'image_url',
            ]);

        expect($response->json('image_url'))->not->toBeNull();
        expect($response->json('image_url'))->toContain('/api/cars/'.$car->id.'/image');

        // Assert file was stored in private storage
        Storage::disk('private')->assertExists('cars/'.$image->hashName());

        // Assert database was updated with the storage path
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'image_url' => 'cars/'.$image->hashName(),
        ]);
    });

    it('replaces existing image when uploading new one', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'image_url' => 'cars/old-image.jpg',
        ]);

        // Create the old image file
        Storage::disk('private')->put('cars/old-image.jpg', 'fake content');

        $newImage = UploadedFile::fake()->image('new-car.jpg', 800, 600);

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $newImage,
        ]);

        $response->assertSuccessful();

        // Assert old image was deleted
        Storage::disk('private')->assertMissing('cars/old-image.jpg');

        // Assert new image was stored
        Storage::disk('private')->assertExists('cars/'.$newImage->hashName());

        // Assert database was updated with new image path
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'image_url' => 'cars/'.$newImage->hashName(),
        ]);
    });

    it('returns 403 when trying to upload image for car owned by another user', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->otherUser->id]);
        $image = UploadedFile::fake()->image('car.jpg');

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $image,
        ]);

        $response->assertForbidden()
            ->assertJson(['message' => 'This action is unauthorized.']);
    });

    it('returns 404 for non-existent car', function () {
        $this->actingAs($this->user);

        $image = UploadedFile::fake()->image('car.jpg');

        $response = $this->postJson('/api/cars/999999/upload-image', [
            'image' => $image,
        ]);

        $response->assertNotFound();
    });

    it('validates image is required', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    });

    it('validates image is actually an image file', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $file,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    });

    it('validates image file size limit', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $image = UploadedFile::fake()->image('car.jpg')->size(11000); // 11MB

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $image,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    });

    it('validates image file type', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $image = UploadedFile::fake()->create('car.bmp', 1000);

        $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
            'image' => $image,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    });

    it('accepts valid image formats', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $formats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        foreach ($formats as $format) {
            $image = UploadedFile::fake()->image("car.{$format}");

            $response = $this->postJson("/api/cars/{$car->id}/upload-image", [
                'image' => $image,
            ]);

            $response->assertSuccessful();
        }
    });
});
