<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Track;

it('displays the track day finder page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('track-day-finder')
        ->has('events')
    );
});

it('shows upcoming events only', function () {
    // Create a past event
    $pastTrack = Track::factory()->create();
    $pastOrganizer = Organizer::factory()->create();
    $pastEvent = Event::factory()->create([
        'track_id' => $pastTrack->id,
        'organizer_id' => $pastOrganizer->id,
        'start_date' => now()->subDays(5),
        'end_date' => now()->subDays(4),
    ]);

    // Create a future event
    $futureTrack = Track::factory()->create();
    $futureOrganizer = Organizer::factory()->create();
    $futureEvent = Event::factory()->create([
        'track_id' => $futureTrack->id,
        'organizer_id' => $futureOrganizer->id,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(6),
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('track-day-finder')
        ->has('events', 1)
        ->where('events.0.id', $futureEvent->id)
        ->where('events.0.title', $futureEvent->title)
        ->has('events.0.track')
        ->where('events.0.track.id', $futureTrack->id)
        ->where('events.0.track.name', $futureTrack->name)
        ->where('events.0.track.latitude', $futureTrack->latitude)
        ->where('events.0.track.longitude', $futureTrack->longitude)
        ->has('events.0.organizer')
        ->where('events.0.organizer.id', $futureOrganizer->id)
        ->where('events.0.organizer.name', $futureOrganizer->name)
    );
});

it('includes all required event data for map display', function () {
    $track = Track::factory()->create([
        'name' => 'Silverstone Circuit',
        'city' => 'Silverstone',
        'country' => 'United Kingdom',
        'latitude' => 52.0786,
        'longitude' => -1.0169,
    ]);

    $organizer = Organizer::factory()->create([
        'name' => 'Track Day Events Ltd',
    ]);

    $event = Event::factory()->create([
        'track_id' => $track->id,
        'organizer_id' => $organizer->id,
        'title' => 'Open Track Day Experience',
        'start_date' => now()->addDays(10),
        'end_date' => now()->addDays(10),
        'website' => 'https://example.com/event',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('track-day-finder')
        ->has('events', 1)
        ->where('events.0.id', $event->id)
        ->where('events.0.title', 'Open Track Day Experience')
        ->where('events.0.website', 'https://example.com/event')
        ->where('events.0.track.name', 'Silverstone Circuit')
        ->where('events.0.track.city', 'Silverstone')
        ->where('events.0.track.country', 'United Kingdom')
        ->where('events.0.track.latitude', 52.0786)
        ->where('events.0.track.longitude', -1.0169)
        ->where('events.0.organizer.name', 'Track Day Events Ltd')
    );
});

it('orders events by start date', function () {
    $track = Track::factory()->create();
    $organizer = Organizer::factory()->create();

    $laterEvent = Event::factory()->create([
        'track_id' => $track->id,
        'organizer_id' => $organizer->id,
        'start_date' => now()->addDays(20),
        'end_date' => now()->addDays(20),
        'title' => 'Later Event',
    ]);

    $earlierEvent = Event::factory()->create([
        'track_id' => $track->id,
        'organizer_id' => $organizer->id,
        'start_date' => now()->addDays(10),
        'end_date' => now()->addDays(10),
        'title' => 'Earlier Event',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('track-day-finder')
        ->has('events', 2)
        ->where('events.0.title', 'Earlier Event')
        ->where('events.1.title', 'Later Event')
    );
});