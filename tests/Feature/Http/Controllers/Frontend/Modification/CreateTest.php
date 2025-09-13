<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
});

describe('ModificationController create method', function () {
    it('requires authentication', function () {
        $response = $this->get("/cars/{$this->car->id}/modifications/create");

        $response->assertRedirect('/login');
    });

    it('renders modification create page for car owner', function () {
        $this->actingAs($this->user);

        $response = $this->get("/cars/{$this->car->id}/modifications/create");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Create')
                ->has('car')
                ->where('car.id', $this->car->id)
            );
    });

    it('forbids access to other users cars', function () {
        $this->actingAs($this->otherUser);

        $response = $this->get("/cars/{$this->car->id}/modifications/create");

        $response->assertForbidden();
    });
});
