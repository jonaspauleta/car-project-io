<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

describe('Modification Model', function () {
    beforeEach(function () {
        /** @var User $this->user */
        $this->user = User::factory()->create();
        /** @var Car $this->car */
        $this->car = Car::factory()->create(['user_id' => $this->user->id]);
    });

    describe('Creation and Mass Assignment', function () {
        it('can be created with valid attributes', function () {
            $modification = Modification::factory()->create([
                'car_id' => $this->car->id,
                'name' => 'Cold Air Intake',
                'category' => 'Engine',
                'notes' => 'Improves airflow and performance',
                'brand' => 'K&N',
                'vendor' => 'AutoZone',
                'installation_date' => '2024-01-15',
                'cost' => 299.99,
                'is_active' => true,
            ]);

            expect($modification->car_id)->toBe($this->car->id);
            expect($modification->name)->toBe('Cold Air Intake');
            expect($modification->category)->toBe('Engine');
            expect($modification->notes)->toBe('Improves airflow and performance');
            expect($modification->brand)->toBe('K&N');
            expect($modification->vendor)->toBe('AutoZone');
            expect($modification->installation_date->format('Y-m-d'))->toBe('2024-01-15');
            expect($modification->cost)->toBe(299.99);
            expect($modification->is_active)->toBeTrue();
        });

        it('can be created with minimal required attributes', function () {
            $modification = Modification::factory()->create([
                'car_id' => $this->car->id,
                'name' => 'Exhaust System',
                'category' => 'Exhaust',
                'notes' => null,
                'brand' => null,
                'vendor' => null,
                'installation_date' => null,
                'cost' => null,
                'is_active' => false,
            ]);

            expect($modification->car_id)->toBe($this->car->id);
            expect($modification->name)->toBe('Exhaust System');
            expect($modification->category)->toBe('Exhaust');
            expect($modification->notes)->toBeNull();
            expect($modification->brand)->toBeNull();
            expect($modification->vendor)->toBeNull();
            expect($modification->installation_date)->toBeNull();
            expect($modification->cost)->toBeNull();
            expect($modification->is_active)->toBeFalse();
        });

        it('respects fillable attributes', function () {
            $modification = new Modification();
            $fillable = $modification->getFillable();

            expect($fillable)->toContain('car_id');
            expect($fillable)->toContain('name');
            expect($fillable)->toContain('category');
            expect($fillable)->toContain('notes');
            expect($fillable)->toContain('brand');
            expect($fillable)->toContain('vendor');
            expect($fillable)->toContain('installation_date');
            expect($fillable)->toContain('cost');
            expect($fillable)->toContain('is_active');
        });
    });

    describe('Relationships', function () {
        it('belongs to a car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($modification->car)->toBeInstanceOf(Car::class);
            expect($modification->car->id)->toBe($this->car->id);
        });

        it('can access car through relationship', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            $car = $modification->car;

            expect($car->make)->toBe($this->car->make);
            expect($car->model)->toBe($this->car->model);
            expect($car->user_id)->toBe($this->user->id);
        });
    });

    describe('Database Constraints', function () {
        it('requires a car_id', function () {
            expect(function () {
                Modification::factory()->create(['car_id' => null]);
            })->toThrow(Exception::class);
        });

        it('requires a name', function () {
            expect(function () {
                Modification::factory()->create(['name' => null]);
            })->toThrow(Exception::class);
        });

        it('requires a category', function () {
            expect(function () {
                Modification::factory()->create(['category' => null]);
            })->toThrow(Exception::class);
        });

        it('allows nullable optional fields', function () {
            $modification = Modification::factory()->create([
                'car_id' => $this->car->id,
                'name' => 'Suspension Upgrade',
                'category' => 'Suspension',
                'notes' => null,
                'brand' => null,
                'vendor' => null,
                'installation_date' => null,
                'cost' => null,
                'is_active' => false,
            ]);

            expect($modification->notes)->toBeNull();
            expect($modification->brand)->toBeNull();
            expect($modification->vendor)->toBeNull();
            expect($modification->installation_date)->toBeNull();
            expect($modification->cost)->toBeNull();
            expect($modification->is_active)->toBeFalse();
        });
    });

    describe('Factory', function () {
        it('creates a modification with factory', function () {
            $modification = Modification::factory()->create();

            expect($modification)->toBeInstanceOf(Modification::class);
            expect($modification->car_id)->not->toBeNull();
            expect($modification->name)->not->toBeEmpty();
            expect($modification->category)->not->toBeEmpty();
        });

        it('creates multiple modifications with factory', function () {
            $modifications = Modification::factory()->count(3)->create();

            expect($modifications)->toHaveCount(3);
            expect($modifications->first())->toBeInstanceOf(Modification::class);
        });

        it('can create modification with specific car', function () {
            $modification = Modification::factory()->create(['car_id' => $this->car->id]);

            expect($modification->car_id)->toBe($this->car->id);
            expect($modification->car)->toBeInstanceOf(Car::class);
        });
    });

    describe('Attributes and Data Types', function () {
        it('casts installation_date as datetime', function () {
            $modification = Modification::factory()->create([
                'installation_date' => '2024-01-15 10:30:00',
            ]);

            expect($modification->installation_date)->toBeInstanceOf(Carbon\CarbonImmutable::class);
            expect($modification->installation_date->format('Y-m-d H:i:s'))->toBe('2024-01-15 10:30:00');
        });

        it('casts cost as float', function () {
            $modification = Modification::factory()->create(['cost' => '299.99']);

            expect($modification->cost)->toBeFloat();
            expect($modification->cost)->toBe(299.99);
        });

        it('casts is_active as boolean', function () {
            $modification = Modification::factory()->create(['is_active' => 1]);

            expect($modification->is_active)->toBeBool();
            expect($modification->is_active)->toBeTrue();

            $modification2 = Modification::factory()->create(['is_active' => 0]);

            expect($modification2->is_active)->toBeBool();
            expect($modification2->is_active)->toBeFalse();
        });

        it('has timestamps', function () {
            $modification = Modification::factory()->create();

            expect($modification->created_at)->not->toBeNull();
            expect($modification->updated_at)->not->toBeNull();
            expect($modification->created_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
            expect($modification->updated_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
        });

        it('can store long text in notes field', function () {
            $longNotes = str_repeat('This is a very long note about the modification. ', 50);
            $modification = Modification::factory()->create(['notes' => $longNotes]);

            expect($modification->notes)->toBe($longNotes);
            expect(mb_strlen($modification->notes))->toBeGreaterThan(500);
        });

        it('can handle decimal cost values', function () {
            $modification = Modification::factory()->create(['cost' => 1234.56]);

            expect($modification->cost)->toBeFloat();
            expect($modification->cost)->toBe(1234.56);
        });

        it('can handle zero cost', function () {
            $modification = Modification::factory()->create(['cost' => 0]);

            expect($modification->cost)->toBeFloat();
            expect($modification->cost)->toBe(0.0);
        });
    });

    describe('Model Methods', function () {
        it('has newFactory method', function () {
            $factory = Modification::newFactory();

            expect($factory)->toBeInstanceOf(Database\Factories\ModificationFactory::class);
        });
    });

    describe('Business Logic', function () {
        it('can be marked as active or inactive', function () {
            $modification = Modification::factory()->create(['is_active' => true]);

            expect($modification->is_active)->toBeTrue();

            $modification->update(['is_active' => false]);

            expect($modification->fresh()->is_active)->toBeFalse();
        });

        it('can track installation date', function () {
            $installationDate = now()->subDays(30);
            $modification = Modification::factory()->create([
                'installation_date' => $installationDate,
            ]);

            expect($modification->installation_date->format('Y-m-d'))
                ->toBe($installationDate->format('Y-m-d'));
        });

        it('can track cost information', function () {
            $modification = Modification::factory()->create(['cost' => 1500.00]);

            expect($modification->cost)->toBe(1500.00);
        });

        it('can store vendor and brand information', function () {
            $modification = Modification::factory()->create([
                'brand' => 'Brembo',
                'vendor' => 'Tire Rack',
            ]);

            expect($modification->brand)->toBe('Brembo');
            expect($modification->vendor)->toBe('Tire Rack');
        });
    });
});
