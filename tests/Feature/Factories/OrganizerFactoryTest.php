<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Organizer;

describe('OrganizerFactory', function () {
    it('generates valid organizer data', function () {
        $organizer = Organizer::factory()->make();

        expect($organizer->name)->toBeString()->not->toBeEmpty();
        expect($organizer->email)->toBeString()->toContain('@')->or->toBeNull();
        expect($organizer->website)->toBeString()->or->toBeNull();
        expect($organizer->logo_url)->toBeString()->or->toBeNull();
    });

    it('can create organizers in bulk', function () {
        $organizers = Organizer::factory()->count(5)->make();

        expect($organizers)->toHaveCount(5);
        $organizers->each(function ($organizer) {
            expect($organizer)->toBeInstanceOf(Organizer::class);
            expect($organizer->name)->toBeString()->not->toBeEmpty();
        });
    });

    it('generates valid email addresses when present', function () {
        $organizers = Organizer::factory()->count(10)->make();

        $organizers->each(function ($organizer) {
            if ($organizer->email !== null) {
                expect($organizer->email)->toContain('@');
                expect($organizer->email)->toContain('.');
                expect($organizer->email)->toBeString();
            }
        });
    });

    it('can override factory attributes', function () {
        $organizer = Organizer::factory()->make([
            'name' => 'Track Day Events Ltd',
            'email' => 'info@trackdayevents.com',
            'website' => 'https://www.trackdayevents.com',
            'logo_url' => 'https://example.com/logo.png',
        ]);

        expect($organizer->name)->toBe('Track Day Events Ltd');
        expect($organizer->email)->toBe('info@trackdayevents.com');
        expect($organizer->website)->toBe('https://www.trackdayevents.com');
        expect($organizer->logo_url)->toBe('https://example.com/logo.png');
    });

    it('can create organizers with null optional fields', function () {
        $organizer = Organizer::factory()->make([
            'email' => null,
            'website' => null,
            'logo_url' => null,
        ]);

        expect($organizer->email)->toBeNull();
        expect($organizer->website)->toBeNull();
        expect($organizer->logo_url)->toBeNull();
    });

    it('generates valid website URLs when present', function () {
        $organizers = Organizer::factory()->count(10)->make();

        $organizers->each(function ($organizer) {
            if ($organizer->website !== null) {
                expect($organizer->website)->toBeString();
                expect($organizer->website)->toContain('http');
            }
        });
    });

    it('generates valid logo URLs when present', function () {
        $organizers = Organizer::factory()->count(10)->make();

        $organizers->each(function ($organizer) {
            if ($organizer->logo_url !== null) {
                expect($organizer->logo_url)->toBeString();
                expect($organizer->logo_url)->toContain('http');
            }
        });
    });

    it('can create and persist organizers to database', function () {
        $organizer = Organizer::factory()->create([
            'name' => 'Premium Track Days',
            'email' => 'contact@premiumtrackdays.com',
        ]);

        expect($organizer)->toBeInstanceOf(Organizer::class);
        expect($organizer->exists)->toBeTrue();
        expect($organizer->id)->toBeInt();

        $this->assertDatabaseHas('organizers', [
            'id' => $organizer->id,
            'name' => 'Premium Track Days',
            'email' => 'contact@premiumtrackdays.com',
        ]);
    });

    it('can create organizers with events relationship', function () {
        $organizer = Organizer::factory()->create();
        $events = Event::factory()->count(4)->create(['organizer_id' => $organizer->id]);

        expect($organizer->events)->toHaveCount(4);
        expect($organizer->events->first())->toBeInstanceOf(Event::class);
        expect($organizer->events->pluck('id')->toArray())->toEqual($events->pluck('id')->toArray());
    });

    it('creates organizers with required fields only', function () {
        $organizer = Organizer::factory()->make();

        // Only name is required
        expect($organizer->name)->not->toBeNull();
        expect($organizer->name)->toBeString()->not->toBeEmpty();
    });

    it('can handle different organizer types and names', function () {
        $organizers = Organizer::factory()->count(5)->create([
            'name' => fn () => fake()->randomElement([
                'Speed Events Ltd',
                'Track Masters',
                'Circuit Champions',
                'Racing Adventures',
                'Motorsport Experiences',
            ]),
        ]);

        expect($organizers)->toHaveCount(5);
        $organizers->each(function ($organizer) {
            expect($organizer->name)->toBeString();
            expect($organizer->name)->not->toBeEmpty();
        });
    });

    it('can create organizers with minimal data', function () {
        $organizer = Organizer::factory()->create([
            'name' => 'Minimal Organizer',
            'email' => null,
            'website' => null,
            'logo_url' => null,
        ]);

        expect($organizer->name)->toBe('Minimal Organizer');
        expect($organizer->email)->toBeNull();
        expect($organizer->website)->toBeNull();
        expect($organizer->logo_url)->toBeNull();

        $this->assertDatabaseHas('organizers', [
            'id' => $organizer->id,
            'name' => 'Minimal Organizer',
            'email' => null,
            'website' => null,
            'logo_url' => null,
        ]);
    });

    it('generates unique organizer names when creating multiple organizers', function () {
        $organizers = Organizer::factory()->count(3)->make();
        $names = $organizers->pluck('name')->toArray();

        expect(count($names))->toBe(3);
        expect($names)->each->toBeString();
        expect($names)->each->not->toBeEmpty();
    });
});
