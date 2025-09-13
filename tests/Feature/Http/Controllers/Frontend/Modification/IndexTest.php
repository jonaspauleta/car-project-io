<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->car = Car::factory()->create(['user_id' => $this->user->id]);
    $this->otherCar = Car::factory()->create(['user_id' => $this->otherUser->id]);
});

describe('ModificationController index method', function () {
    it('requires authentication', function () {
        $response = $this->get("/cars/{$this->car->id}/modifications");

        $response->assertRedirect('/login');
    });

    it('renders modifications index page for car owner', function () {
        $this->actingAs($this->user);

        Modification::factory()->count(3)->create(['car_id' => $this->car->id]);

        $response = $this->get("/cars/{$this->car->id}/modifications");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
                ->has('car')
                ->where('car.id', $this->car->id)
                ->has('modifications.data', 3)
            );
    });

    it('forbids access to other users cars', function () {
        $this->actingAs($this->otherUser);

        $response = $this->get("/cars/{$this->car->id}/modifications");

        $response->assertForbidden();
    });

    it('supports search filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Performance Exhaust'
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Sport Suspension'
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?search=Exhaust");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });

    it('supports name filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Performance Exhaust'
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Sport Suspension'
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?filter[name]=Performance Exhaust");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });

    it('supports category filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'category' => 'Performance'
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'category' => 'Cosmetic'
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?filter[category]=Performance");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });

    it('supports brand filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'brand' => 'Borla'
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'brand' => 'MagnaFlow'
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?filter[brand]=Borla");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });

    it('supports vendor filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'vendor' => 'Amazon'
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'vendor' => 'eBay'
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?filter[vendor]=Amazon");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });

    it('supports is_active filtering', function () {
        $this->actingAs($this->user);

        Modification::factory()->create([
            'car_id' => $this->car->id,
            'is_active' => true
        ]);
        Modification::factory()->create([
            'car_id' => $this->car->id,
            'is_active' => false
        ]);

        $response = $this->get("/cars/{$this->car->id}/modifications?filter[is_active]=1");

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Modifications/Index')
            );
    });
});
