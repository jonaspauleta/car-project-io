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
        $response = $this->getJson("/api/cars/{$this->car->id}/modifications");

        $response->assertUnauthorized();
    });

    it('returns paginated list of modifications for authenticated user cars', function () {
        $this->actingAs($this->user);

        Modification::factory()->count(3)->create(['car_id' => $this->car->id]);
        Modification::factory()->count(2)->create(['car_id' => $this->otherCar->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'car_id',
                        'name',
                        'category',
                        'notes',
                        'brand',
                        'vendor',
                        'installation_date',
                        'cost',
                        'is_active',
                    ],
                ],
                'links',
                'meta',
            ]);

        expect($response->json('data'))->toHaveCount(3);
    });

    it('supports pagination', function () {
        $this->actingAs($this->user);

        Modification::factory()->count(15)->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?page=2&per_page=5");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(5);
        expect($response->json('meta.current_page'))->toBe(2);
    });

    it('supports filtering by name', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'name' => 'Cold Air Intake']);
        Modification::factory()->create(['car_id' => $this->car->id, 'name' => 'Exhaust System']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?filter[name]=Cold Air Intake");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.name'))->toBe('Cold Air Intake');
    });

    it('supports filtering by category', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'category' => 'Engine']);
        Modification::factory()->create(['car_id' => $this->car->id, 'category' => 'Exhaust']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?filter[category]=Engine");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.category'))->toBe('Engine');
    });

    it('supports filtering by brand', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'brand' => 'K&N']);
        Modification::factory()->create(['car_id' => $this->car->id, 'brand' => 'Borla']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?filter[brand]=K&N");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.brand'))->toBe('K&N');
    });

    it('supports filtering by vendor', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'vendor' => 'AutoZone']);
        Modification::factory()->create(['car_id' => $this->car->id, 'vendor' => 'Amazon']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?filter[vendor]=AutoZone");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.vendor'))->toBe('AutoZone');
    });

    it('supports filtering by is_active', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'is_active' => true]);
        Modification::factory()->create(['car_id' => $this->car->id, 'is_active' => false]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?filter[is_active]=true");

        $response->assertSuccessful();
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.is_active'))->toBe(true);
    });

    it('supports sorting by id', function () {
        $this->actingAs($this->user);

        $mod1 = Modification::factory()->create(['car_id' => $this->car->id]);
        $mod2 = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?sort=id");

        $response->assertSuccessful();
        expect($response->json('data.0.id'))->toBe($mod1->id);
        expect($response->json('data.1.id'))->toBe($mod2->id);
    });

    it('supports sorting by name', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'name' => 'Zebra Intake']);
        Modification::factory()->create(['car_id' => $this->car->id, 'name' => 'Apple Exhaust']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?sort=name");

        $response->assertSuccessful();
        expect($response->json('data.0.name'))->toBe('Apple Exhaust');
        expect($response->json('data.1.name'))->toBe('Zebra Intake');
    });

    it('supports sorting by category', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'category' => 'Zebra']);
        Modification::factory()->create(['car_id' => $this->car->id, 'category' => 'Apple']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?sort=category");

        $response->assertSuccessful();
        expect($response->json('data.0.category'))->toBe('Apple');
        expect($response->json('data.1.category'))->toBe('Zebra');
    });

    it('supports sorting by cost', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'cost' => 500.00]);
        Modification::factory()->create(['car_id' => $this->car->id, 'cost' => 200.00]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?sort=cost");

        $response->assertSuccessful();
        expect($response->json('data.0.cost'))->toBe(200);
        expect($response->json('data.1.cost'))->toBe(500);
    });

    it('supports sorting by installation_date', function () {
        $this->actingAs($this->user);

        Modification::factory()->create(['car_id' => $this->car->id, 'installation_date' => '2023-06-15']);
        Modification::factory()->create(['car_id' => $this->car->id, 'installation_date' => '2023-05-10']);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?sort=installation_date");

        $response->assertSuccessful();
        expect($response->json('data.0.installation_date'))->toContain('2023-05-10');
        expect($response->json('data.1.installation_date'))->toContain('2023-06-15');
    });

    it('includes car relationship when requested', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications?include[]=car");

        $response->assertSuccessful();
        expect($response->json('data.0.car'))->not->toBeNull();
        expect($response->json('data.0.car.id'))->toBe($this->car->id);
    });
});
