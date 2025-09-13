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

describe('ModificationController edit method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}/edit");

        $response->assertRedirect('/login');
    });

    it('renders modification edit page for car owner', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}/edit");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Edit')
                ->has('car')
                ->where('car.id', $this->car->id)
                ->has('modification')
                ->where('modification.id', $modification->id)
            );
    });

    it('forbids access to other users modifications', function () {
        $this->actingAs($this->otherUser);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}/edit");

        $response->assertForbidden();
    });
});
