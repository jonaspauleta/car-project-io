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

describe('ModificationController show method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertRedirect('/login');
    });

    it('shows modification details for car owner', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Show')
                ->has('car')
                ->where('car.id', $this->car->id)
                ->has('modification')
                ->where('modification.id', $modification->id)
                ->has('modification.car')
            );
    });

    it('forbids access to other users modifications', function () {
        $this->actingAs($this->otherUser);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertForbidden();
    });

    it('loads car relationship', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Show')
                ->where('modification.car.id', $this->car->id)
            );
    });
});
