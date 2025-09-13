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

describe('index method', function () {
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

describe('show method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertUnauthorized();
    });

    it('returns modification details for authenticated user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
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
            ]);

        expect($response->json('id'))->toBe($modification->id);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/999999");

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNotFound();
    });

    it('includes car relationship when requested', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->getJson("/api/cars/{$this->car->id}/modifications/{$modification->id}?include[]=car");

        $response->assertSuccessful();
        expect($response->json('car'))->not->toBeNull();
        expect($response->json('car.id'))->toBe($this->car->id);
    });
});

describe('store method', function () {
    it('requires authentication', function () {
        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertUnauthorized();
    });

    it('creates a new modification with valid data', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'notes' => 'Increases airflow and performance',
            'brand' => 'K&N',
            'vendor' => 'AutoZone',
            'installation_date' => '2023-06-15T10:30:00Z',
            'cost' => 299.99,
            'is_active' => true,
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertCreated()
            ->assertJsonStructure([
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
            ]);

        expect($response->json('name'))->toBe('Cold Air Intake');
        expect($response->json('category'))->toBe('Engine');
        expect($response->json('cost'))->toBe(299.99);

        $this->assertDatabaseHas('modifications', [
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
        ]);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category']);
    });

    it('validates name is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 123,
            'category' => 'Engine',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates category is string', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 123,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    });

    it('validates cost is numeric and non-negative', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => -100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cost']);
    });

    it('validates installation_date is valid date', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'installation_date' => 'not-a-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['installation_date']);
    });

    it('validates is_active is boolean', function () {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'is_active' => 'not-boolean',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['is_active']);
    });

    it('allows optional fields to be null', function () {
        $this->actingAs($this->user);

        $modificationData = [
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'notes' => null,
            'brand' => null,
            'vendor' => null,
            'installation_date' => null,
            'cost' => null,
        ];

        $response = $this->postJson("/api/cars/{$this->car->id}/modifications", $modificationData);

        $response->assertCreated();
        expect($response->json('notes'))->toBeNull();
        expect($response->json('brand'))->toBeNull();
        expect($response->json('vendor'))->toBeNull();
        expect($response->json('installation_date'))->toBeNull();
        expect($response->json('cost'))->toBeNull();
        expect($response->json('is_active'))->toBe(true); // default value
    });
});

describe('update method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertUnauthorized();
    });

    it('updates modification with valid data', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => 299.99,
        ]);

        $updateData = [
            'name' => 'High Flow Air Intake',
            'category' => 'Performance',
            'cost' => 399.99,
            'brand' => 'K&N',
        ];

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", $updateData);

        $response->assertSuccessful()
            ->assertJsonStructure([
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
            ]);

        expect($response->json('name'))->toBe('High Flow Air Intake');
        expect($response->json('category'))->toBe('Performance');
        expect($response->json('cost'))->toBe(399.99);
        expect($response->json('brand'))->toBe('K&N');

        $this->assertDatabaseHas('modifications', [
            'id' => $modification->id,
            'name' => 'High Flow Air Intake',
            'category' => 'Performance',
            'cost' => 399.99,
        ]);
    });

    it('validates car_id exists when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'car_id' => 999999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['car_id']);
    });

    it('validates cost is numeric and non-negative when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'cost' => -100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cost']);
    });

    it('validates installation_date is valid date when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'installation_date' => 'not-a-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['installation_date']);
    });

    it('validates is_active is boolean when provided', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'is_active' => 'not-boolean',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['is_active']);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/999999", [
            'name' => 'Updated Name',
        ]);

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertNotFound();
    });

    it('allows partial updates', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create([
            'car_id' => $this->car->id,
            'name' => 'Cold Air Intake',
            'category' => 'Engine',
            'cost' => 299.99,
        ]);

        $response = $this->putJson("/api/cars/{$this->car->id}/modifications/{$modification->id}", [
            'name' => 'High Flow Air Intake',
        ]);

        $response->assertSuccessful();
        expect($response->json('name'))->toBe('High Flow Air Intake');
        expect($response->json('category'))->toBe('Engine'); // unchanged
        expect($response->json('cost'))->toBe(299.99); // unchanged
    });
});

describe('destroy method', function () {
    it('requires authentication', function () {
        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertUnauthorized();
    });

    it('deletes modification successfully', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->car->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('modifications', [
            'id' => $modification->id,
        ]);
    });

    it('returns 404 for non-existent modification', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/999999");

        $response->assertNotFound();
    });

    it('returns 404 for modification belonging to other user', function () {
        $this->actingAs($this->user);

        $modification = Modification::factory()->create(['car_id' => $this->otherCar->id]);

        $response = $this->deleteJson("/api/cars/{$this->car->id}/modifications/{$modification->id}");

        $response->assertNotFound();
    });
});
