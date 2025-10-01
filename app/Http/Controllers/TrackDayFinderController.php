<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TrackDayFinderController extends Controller
{
    public function __invoke(): InertiaResponse
    {
        $events = Event::with(['track', 'organizer'])
            ->whereDate('start_date', '>=', now())
            ->orderBy('start_date')
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => Carbon::parse($event->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($event->end_date)->format('Y-m-d'),
                    'website' => $event->website,
                    'track' => [
                        'id' => $event->track->id,
                        'name' => $event->track->name,
                        'city' => $event->track->city,
                        'country' => $event->track->country,
                        'latitude' => $event->track->latitude,
                        'longitude' => $event->track->longitude,
                    ],
                    'organizer' => [
                        'id' => $event->organizer->id,
                        'name' => $event->organizer->name,
                    ],
                ];
            });

        return Inertia::render('track-day-finder', [
            'events' => $events,
        ]);
    }
}
