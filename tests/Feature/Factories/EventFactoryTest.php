<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Track;

describe('EventFactory', function () {
    it('generates valid event data', function () {
        $event = Event::factory()->make();

        expect($event->track_id)->toBeInt();
        expect($event->organizer_id)->toBeInt();
        expect($event->title)->toBeString()->not->toBeEmpty();
        expect($event->description)->toBeString()->or->toBeNull();
        expect($event->start_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
        expect($event->end_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
        expect($event->website)->toBeString()->or->toBeNull();
    });

    it('creates related models automatically', function () {
        $event = Event::factory()->create();

        expect($event->track)->toBeInstanceOf(Track::class);
        expect($event->organizer)->toBeInstanceOf(Organizer::class);
        expect($event->track->id)->toBe($event->track_id);
        expect($event->organizer->id)->toBe($event->organizer_id);
    });

    it('can create events in bulk', function () {
        $events = Event::factory()->count(3)->make();

        expect($events)->toHaveCount(3);
        $events->each(function ($event) {
            expect($event)->toBeInstanceOf(Event::class);
            expect($event->title)->toBeString()->not->toBeEmpty();
            expect($event->start_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
            expect($event->end_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
        });
    });

    it('generates valid date objects', function () {
        $event = Event::factory()->make();

        // Check that dates are Carbon objects and can be formatted properly
        expect($event->start_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
        expect($event->end_date)->toBeInstanceOf(Carbon\CarbonInterface::class);
        expect($event->start_date->format('Y-m-d'))->toMatch('/^\d{4}-\d{2}-\d{2}$/');
        expect($event->end_date->format('Y-m-d'))->toMatch('/^\d{4}-\d{2}-\d{2}$/');
    });

    it('can use existing track and organizer', function () {
        $track = Track::factory()->create(['name' => 'Silverstone Circuit']);
        $organizer = Organizer::factory()->create(['name' => 'Pro Track Days']);

        $event = Event::factory()->create([
            'track_id' => $track->id,
            'organizer_id' => $organizer->id,
        ]);

        expect($event->track_id)->toBe($track->id);
        expect($event->organizer_id)->toBe($organizer->id);
        expect($event->track->name)->toBe('Silverstone Circuit');
        expect($event->organizer->name)->toBe('Pro Track Days');
    });

    it('can override factory attributes', function () {
        $event = Event::factory()->make([
            'title' => 'Advanced Track Day Experience',
            'description' => 'High-performance track day for experienced drivers',
            'start_date' => '2025-10-15',
            'end_date' => '2025-10-15',
            'website' => 'https://www.trackday.com/advanced',
        ]);

        expect($event->title)->toBe('Advanced Track Day Experience');
        expect($event->description)->toBe('High-performance track day for experienced drivers');
        expect($event->start_date->format('Y-m-d'))->toBe('2025-10-15');
        expect($event->end_date->format('Y-m-d'))->toBe('2025-10-15');
        expect($event->website)->toBe('https://www.trackday.com/advanced');
    });

    it('can create events with null optional fields', function () {
        $event = Event::factory()->make([
            'description' => null,
            'website' => null,
        ]);

        expect($event->description)->toBeNull();
        expect($event->website)->toBeNull();
    });

    it('generates valid website URLs when present', function () {
        $events = Event::factory()->count(10)->make();

        $events->each(function ($event) {
            if ($event->website !== null) {
                expect($event->website)->toBeString();
                expect($event->website)->toContain('http');
            }
        });
    });

    it('can create and persist events to database', function () {
        $event = Event::factory()->create([
            'title' => 'Beginner Track Day',
            'start_date' => '2025-11-20',
            'end_date' => '2025-11-20',
        ]);

        expect($event)->toBeInstanceOf(Event::class);
        expect($event->exists)->toBeTrue();
        expect($event->id)->toBeInt();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Beginner Track Day',
            'start_date' => '2025-11-20 00:00:00',
            'end_date' => '2025-11-20 00:00:00',
        ]);
    });

    it('creates events with all required fields', function () {
        $event = Event::factory()->make();

        // Required fields should never be null
        expect($event->track_id)->not->toBeNull();
        expect($event->organizer_id)->not->toBeNull();
        expect($event->title)->not->toBeNull();
        expect($event->start_date)->not->toBeNull();
        expect($event->end_date)->not->toBeNull();
    });

    it('can create events with same track but different organizers', function () {
        $track = Track::factory()->create();
        $organizer1 = Organizer::factory()->create();
        $organizer2 = Organizer::factory()->create();

        $event1 = Event::factory()->create([
            'track_id' => $track->id,
            'organizer_id' => $organizer1->id,
            'title' => 'Morning Session',
        ]);

        $event2 = Event::factory()->create([
            'track_id' => $track->id,
            'organizer_id' => $organizer2->id,
            'title' => 'Afternoon Session',
        ]);

        expect($event1->track_id)->toBe($track->id);
        expect($event2->track_id)->toBe($track->id);
        expect($event1->organizer_id)->toBe($organizer1->id);
        expect($event2->organizer_id)->toBe($organizer2->id);
        expect($event1->organizer_id)->not->toBe($event2->organizer_id);
    });

    it('can create events with same organizer but different tracks', function () {
        $track1 = Track::factory()->create();
        $track2 = Track::factory()->create();
        $organizer = Organizer::factory()->create();

        $event1 = Event::factory()->create([
            'track_id' => $track1->id,
            'organizer_id' => $organizer->id,
            'title' => 'Circuit A Event',
        ]);

        $event2 = Event::factory()->create([
            'track_id' => $track2->id,
            'organizer_id' => $organizer->id,
            'title' => 'Circuit B Event',
        ]);

        expect($event1->organizer_id)->toBe($organizer->id);
        expect($event2->organizer_id)->toBe($organizer->id);
        expect($event1->track_id)->toBe($track1->id);
        expect($event2->track_id)->toBe($track2->id);
        expect($event1->track_id)->not->toBe($event2->track_id);
    });

    it('can create multi-day events', function () {
        $event = Event::factory()->create([
            'title' => 'Weekend Track Experience',
            'start_date' => '2025-08-15',
            'end_date' => '2025-08-17',
        ]);

        expect($event->start_date->format('Y-m-d'))->toBe('2025-08-15');
        expect($event->end_date->format('Y-m-d'))->toBe('2025-08-17');
        expect($event->title)->toBe('Weekend Track Experience');
    });

    it('can create single-day events', function () {
        $event = Event::factory()->create([
            'title' => 'Single Day Experience',
            'start_date' => '2025-09-10',
            'end_date' => '2025-09-10',
        ]);

        expect($event->start_date->format('Y-m-d'))->toBe('2025-09-10');
        expect($event->end_date->format('Y-m-d'))->toBe('2025-09-10');
    });

    it('generates realistic event titles', function () {
        $events = Event::factory()->count(5)->make();

        $events->each(function ($event) {
            expect($event->title)->toBeString()->not->toBeEmpty();
            // The factory uses faker->name() which might not be ideal for event titles,
            // but we're testing that it generates something
        });
    });

    it('can create events with complex scenarios', function () {
        // Create a track with multiple events from different organizers
        $track = Track::factory()->create(['name' => 'Portimão Circuit']);
        $organizer1 = Organizer::factory()->create(['name' => 'Speed Events']);
        $organizer2 = Organizer::factory()->create(['name' => 'Track Masters']);

        $events1 = Event::factory()->count(2)->create([
            'track_id' => $track->id,
            'organizer_id' => $organizer1->id,
        ]);

        $events2 = Event::factory()->count(1)->create([
            'track_id' => $track->id,
            'organizer_id' => $organizer2->id,
        ]);

        // Verify relationships
        expect($track->events)->toHaveCount(3);
        expect($organizer1->events)->toHaveCount(2);
        expect($organizer2->events)->toHaveCount(1);

        // All events should belong to the same track
        $track->events->each(function ($event) use ($track) {
            expect($event->track_id)->toBe($track->id);
        });
    });
});
