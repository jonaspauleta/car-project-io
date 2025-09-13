<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController edit method', function () {
    it('requires authentication', function () {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}/edit");

        $response->assertRedirect('/login');
    });

    it('renders car edit page for owner', function () {
        $this->actingAs($this->user);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}/edit");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Edit')
                ->has('car')
                ->where('car.id', $car->id)
            );
    });

    it('forbids access to other users cars', function () {
        $this->actingAs($this->otherUser);

        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/cars/{$car->id}/edit");

        $response->assertForbidden();
    });
});
