<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('CarController create method', function () {
    it('requires authentication', function () {
        $response = $this->get('/cars/create');

        $response->assertRedirect('/login');
    });

    it('renders car create page for authenticated user', function () {
        $this->actingAs($this->user);

        $response = $this->get('/cars/create');

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Cars/Create')
            );
    });
});
