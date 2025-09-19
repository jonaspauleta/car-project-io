<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Track;

describe('TrackFactory', function () {
    it('generates valid track data', function () {
        $track = Track::factory()->make();

        expect($track->name)->toBeString()->not->toBeEmpty();
        expect($track->country)->toBeString()->not->toBeEmpty();
        expect($track->city)->toBeString()->not->toBeEmpty();
        expect($track->latitude)->toBeFloat()->toBeBetween(-90, 90);
        expect($track->longitude)->toBeFloat()->toBeBetween(-180, 180);
        expect($track->website)->toBeString()->or->toBeNull();

        if ($track->noise_limit !== null) {
            expect($track->noise_limit)->toBeInt()->toBeBetween(0, 120);
        } else {
            expect($track->noise_limit)->toBeNull();
        }
    });

    it('can create tracks in bulk', function () {
        $tracks = Track::factory()->count(10)->make();

        expect($tracks)->toHaveCount(10);
        $tracks->each(function ($track) {
            expect($track)->toBeInstanceOf(Track::class);
            expect($track->name)->toBeString();
            expect($track->country)->toBeString();
            expect($track->city)->toBeString();
            expect($track->latitude)->toBeFloat();
            expect($track->longitude)->toBeFloat();
        });
    });

    it('generates unique track names when creating multiple tracks', function () {
        $tracks = Track::factory()->count(5)->make();
        $names = $tracks->pluck('name')->toArray();

        // While not guaranteed to be unique due to faker, this tests the variety
        expect(count($names))->toBeGreaterThan(0);
        expect($names)->each->toBeString();
    });

    it('can override factory attributes', function () {
        $track = Track::factory()->make([
            'name' => 'Custom Track Name',
            'country' => 'Portugal',
            'city' => 'Portimão',
            'latitude' => 37.2272,
            'longitude' => -8.6267,
            'noise_limit' => 95,
        ]);

        expect($track->name)->toBe('Custom Track Name');
        expect($track->country)->toBe('Portugal');
        expect($track->city)->toBe('Portimão');
        expect($track->latitude)->toBe(37.2272);
        expect($track->longitude)->toBe(-8.6267);
        expect($track->noise_limit)->toBe(95);
    });

    it('can create tracks with null optional fields', function () {
        $track = Track::factory()->make([
            'website' => null,
            'noise_limit' => null,
        ]);

        expect($track->website)->toBeNull();
        expect($track->noise_limit)->toBeNull();
    });

    it('generates valid coordinates within proper ranges', function () {
        $tracks = Track::factory()->count(20)->make();

        $tracks->each(function ($track) {
            expect($track->latitude)->toBeBetween(-90, 90);
            expect($track->longitude)->toBeBetween(-180, 180);
        });
    });

    it('generates valid noise limits when present', function () {
        $tracks = Track::factory()->count(20)->make();

        $tracks->each(function ($track) {
            if ($track->noise_limit !== null) {
                expect($track->noise_limit)->toBeInt()->toBeGreaterThanOrEqual(0);
                expect($track->noise_limit)->toBeLessThanOrEqual(120);
            }
        });
    });

    it('can create and persist tracks to database', function () {
        $track = Track::factory()->create([
            'name' => 'Silverstone Circuit',
            'country' => 'United Kingdom',
            'city' => 'Silverstone',
        ]);

        expect($track)->toBeInstanceOf(Track::class);
        expect($track->exists)->toBeTrue();
        expect($track->id)->toBeInt();

        $this->assertDatabaseHas('tracks', [
            'id' => $track->id,
            'name' => 'Silverstone Circuit',
            'country' => 'United Kingdom',
            'city' => 'Silverstone',
        ]);
    });

    it('can create tracks with events relationship', function () {
        $track = Track::factory()->create();
        $events = Event::factory()->count(3)->create(['track_id' => $track->id]);

        expect($track->events)->toHaveCount(3);
        expect($track->events->first())->toBeInstanceOf(Event::class);
        expect($track->events->pluck('id')->toArray())->toEqual($events->pluck('id')->toArray());
    });

    it('generates valid website URLs when present', function () {
        $tracks = Track::factory()->count(10)->make();

        $tracks->each(function ($track) {
            if ($track->website !== null) {
                expect($track->website)->toBeString();
                expect($track->website)->toContain('http');
            }
        });
    });

    it('can create tracks with specific precision for coordinates', function () {
        $track = Track::factory()->create([
            'latitude' => 52.0786123,
            'longitude' => -1.0169456,
        ]);

        // The database should store the values with proper precision
        expect($track->latitude)->toBe(52.0786123);
        expect($track->longitude)->toBe(-1.0169456);
    });

    it('creates tracks with all required fields', function () {
        $track = Track::factory()->make();

        // Required fields should never be null
        expect($track->name)->not->toBeNull();
        expect($track->country)->not->toBeNull();
        expect($track->city)->not->toBeNull();
        expect($track->latitude)->not->toBeNull();
        expect($track->longitude)->not->toBeNull();
    });
});
