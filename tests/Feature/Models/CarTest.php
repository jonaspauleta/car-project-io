<?php

declare(strict_types=1);

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

describe('Car Model', function () {
    beforeEach(function () {
        /** @var User $this->user */
        $this->user = User::factory()->create();
    });

    describe('Creation and Mass Assignment', function () {
        it('can be created with valid attributes', function () {
            $car = Car::factory()->create([
                'user_id' => $this->user->id,
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2020,
                'nickname' => 'My Car',
                'vin' => '1HGBH41JXMN109186',
                'image_url' => 'https://example.com/car.jpg',
                'notes' => 'This is my favorite car',
            ]);

            expect($car->user_id)->toBe($this->user->id);
            expect($car->make)->toBe('Toyota');
            expect($car->model)->toBe('Camry');
            expect($car->year)->toBe(2020);
            expect($car->nickname)->toBe('My Car');
            expect($car->vin)->toBe('1HGBH41JXMN109186');
            expect($car->image_url)->toBe('https://example.com/car.jpg');
            expect($car->notes)->toBe('This is my favorite car');
        });

        it('can be created with minimal required attributes', function () {
            $car = Car::factory()->create([
                'user_id' => $this->user->id,
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2019,
                'nickname' => null,
                'vin' => null,
                'image_url' => null,
                'notes' => null,
            ]);

            expect($car->user_id)->toBe($this->user->id);
            expect($car->make)->toBe('Honda');
            expect($car->model)->toBe('Civic');
            expect($car->year)->toBe(2019);
            expect($car->nickname)->toBeNull();
            expect($car->vin)->toBeNull();
            expect($car->image_url)->toBeNull();
            expect($car->notes)->toBeNull();
        });

        it('respects fillable attributes', function () {
            $car = new Car();
            $fillable = $car->getFillable();

            expect($fillable)->toContain('user_id');
            expect($fillable)->toContain('make');
            expect($fillable)->toContain('model');
            expect($fillable)->toContain('year');
            expect($fillable)->toContain('nickname');
            expect($fillable)->toContain('vin');
            expect($fillable)->toContain('image_url');
            expect($fillable)->toContain('notes');
        });
    });

    describe('Relationships', function () {
        it('belongs to a user', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($car->user)->toBeInstanceOf(User::class);
            expect($car->user->id)->toBe($this->user->id);
        });

        it('has many modifications', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);
            $modification1 = Modification::factory()->create(['car_id' => $car->id]);
            $modification2 = Modification::factory()->create(['car_id' => $car->id]);

            $modifications = $car->modifications;

            expect($modifications)->toHaveCount(2);
            expect($modifications->first())->toBeInstanceOf(Modification::class);
            expect($modifications->pluck('id')->toArray())->toContain($modification1->id, $modification2->id);
        });

        it('can have no modifications', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($car->modifications)->toHaveCount(0);
        });
    });

    describe('Database Constraints', function () {
        it('requires a user_id', function () {
            expect(function () {
                Car::factory()->create(['user_id' => null]);
            })->toThrow(Exception::class);
        });

        it('requires a make', function () {
            expect(function () {
                Car::factory()->create(['make' => null]);
            })->toThrow(Exception::class);
        });

        it('requires a model', function () {
            expect(function () {
                Car::factory()->create(['model' => null]);
            })->toThrow(Exception::class);
        });

        it('requires a year', function () {
            expect(function () {
                Car::factory()->create(['year' => null]);
            })->toThrow(Exception::class);
        });

        it('allows nullable optional fields', function () {
            $car = Car::factory()->create([
                'user_id' => $this->user->id,
                'make' => 'Ford',
                'model' => 'Focus',
                'year' => 2018,
                'nickname' => null,
                'vin' => null,
                'image_url' => null,
                'notes' => null,
            ]);

            expect($car->nickname)->toBeNull();
            expect($car->vin)->toBeNull();
            expect($car->image_url)->toBeNull();
            expect($car->notes)->toBeNull();
        });
    });

    describe('Factory', function () {
        it('creates a car with factory', function () {
            $car = Car::factory()->create();

            expect($car)->toBeInstanceOf(Car::class);
            expect($car->user_id)->not->toBeNull();
            expect($car->make)->not->toBeEmpty();
            expect($car->model)->not->toBeEmpty();
            expect($car->year)->toBeNumeric();
            expect((int) $car->year)->toBeGreaterThan(1900);
            expect((int) $car->year)->toBeLessThanOrEqual((int) date('Y') + 1);
        });

        it('creates multiple cars with factory', function () {
            $cars = Car::factory()->count(3)->create();

            expect($cars)->toHaveCount(3);
            expect($cars->first())->toBeInstanceOf(Car::class);
        });

        it('can create car with specific user', function () {
            $car = Car::factory()->create(['user_id' => $this->user->id]);

            expect($car->user_id)->toBe($this->user->id);
            expect($car->user)->toBeInstanceOf(User::class);
        });
    });

    describe('Attributes and Data Types', function () {
        it('casts year as integer', function () {
            $car = Car::factory()->create(['year' => '2020']);

            expect($car->year)->toBeNumeric();
            expect((int) $car->year)->toBe(2020);
        });

        it('has timestamps', function () {
            $car = Car::factory()->create();

            expect($car->created_at)->not->toBeNull();
            expect($car->updated_at)->not->toBeNull();
            expect($car->created_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
            expect($car->updated_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
        });

        it('can store long text in notes field', function () {
            $longNotes = str_repeat('This is a very long note. ', 100);
            $car = Car::factory()->create(['notes' => $longNotes]);

            expect($car->notes)->toBe($longNotes);
            expect(mb_strlen($car->notes))->toBeGreaterThan(1000);
        });
    });

    describe('Model Methods', function () {
        it('has newFactory method', function () {
            $factory = Car::newFactory();

            expect($factory)->toBeInstanceOf(Database\Factories\CarFactory::class);
        });
    });
});
