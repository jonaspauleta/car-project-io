<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('CarController index method', function () {
    it('requires authentication', function () {
        $response = $this->get('/cars');

        $response->assertRedirect('/login');
    });

    it('renders cars index page for authenticated user', function () {
        $this->actingAs($this->user);

        Car::factory()->count(3)->create(['user_id' => $this->user->id]);
        Car::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

        $response = $this->get('/cars');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Index')
                ->has('cars.data', 3)
            );
    });

    it('supports search filtering', function () {
        $this->actingAs($this->user);

        Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Toyota',
            'model' => 'Camry'
        ]);
        Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Honda',
            'model' => 'Civic'
        ]);

        $response = $this->get('/cars?search=Toyota');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Index')
            );
    });

    it('supports make filtering', function () {
        $this->actingAs($this->user);

        Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Toyota'
        ]);
        Car::factory()->create([
            'user_id' => $this->user->id,
            'make' => 'Honda'
        ]);

        $response = $this->get('/cars?filter[make]=Toyota');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Index')
            );
    });

    it('supports model filtering', function () {
        $this->actingAs($this->user);

        Car::factory()->create([
            'user_id' => $this->user->id,
            'model' => 'Camry'
        ]);
        Car::factory()->create([
            'user_id' => $this->user->id,
            'model' => 'Civic'
        ]);

        $response = $this->get('/cars?filter[model]=Camry');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Index')
            );
    });

    it('supports year filtering', function () {
        $this->actingAs($this->user);

        Car::factory()->create([
            'user_id' => $this->user->id,
            'year' => 2020
        ]);
        Car::factory()->create([
            'user_id' => $this->user->id,
            'year' => 2021
        ]);

        $response = $this->get('/cars?filter[year]=2020');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Index')
            );
    });
});
