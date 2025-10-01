import EventsCalendar from '@/components/events-calendar';
import TrackMap from '@/components/track-map';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

interface Event {
    id: number;
    title: string;
    start_date: string;
    end_date: string;
    website?: string;
    track: {
        id: number;
        name: string;
        city: string;
        country: string;
        latitude: number;
        longitude: number;
    };
    organizer: {
        id: number;
        name: string;
    };
}

interface TrackDayFinderProps {
    events: Event[];
}

export default function TrackDayFinder({ events }: TrackDayFinderProps) {
    const { auth } = usePage<SharedData>().props;
    const [filteredTrackId, setFilteredTrackId] = useState<number | null>(null);

    const handleFilterTrack = (trackId: number) => {
        setFilteredTrackId(trackId);
    };

    const handleClearFilter = () => {
        setFilteredTrackId(null);
    };

    return (
        <>
            <Head title="Track Day Finder" />

            <div className="min-h-screen bg-background">
                {/* Header */}
                <header className="border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                    <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center space-x-4">
                                <div className="flex items-center space-x-2">
                                    <svg
                                        className="h-8 w-8 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                    <h1 className="text-xl font-bold text-foreground">
                                        Track Day Finder
                                    </h1>
                                </div>
                            </div>
                            <nav className="flex items-center space-x-4">
                                {auth.user ? (
                                    <Button asChild>
                                        <Link href={dashboard()}>
                                            Dashboard
                                        </Link>
                                    </Button>
                                ) : (
                                    <>
                                        <Button variant="ghost" asChild>
                                            <Link href={login()}>Log in</Link>
                                        </Button>
                                        <Button asChild>
                                            <Link href={register()}>
                                                Register
                                            </Link>
                                        </Button>
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="border-b border-border bg-gradient-to-b from-background to-muted/20">
                    <div className="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
                        <div className="space-y-4 text-center">
                            <h2 className="text-3xl font-bold tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                                Find Your Next Track Day Adventure
                            </h2>
                            <p className="mx-auto max-w-2xl text-lg text-muted-foreground">
                                Discover track day events around the world.
                                Click on any marker to see event details and get
                                more information.
                            </p>
                            <div className="flex items-center justify-center space-x-4 pt-4">
                                <Badge variant="secondary" className="text-sm">
                                    {events.length} Events Available
                                </Badge>
                                <Badge variant="outline" className="text-sm">
                                    Real-time Updates
                                </Badge>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Map Section */}
                <section className="py-12">
                    <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                        <TrackMap
                            events={events}
                            onFilterTrack={handleFilterTrack}
                        />
                    </div>
                </section>

                {/* Calendar Section */}
                {events.length > 0 && (
                    <section className="bg-muted/30 py-12">
                        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                            <EventsCalendar
                                events={events}
                                filteredTrackId={filteredTrackId}
                                onClearFilter={handleClearFilter}
                            />
                        </div>
                    </section>
                )}

                {/* Empty State */}
                {events.length === 0 && (
                    <section className="py-24">
                        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                            <Card>
                                <CardContent className="py-16">
                                    <div className="space-y-4 text-center">
                                        <svg
                                            className="mx-auto h-16 w-16 text-muted-foreground"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={1}
                                                d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0a1 1 0 01-1 1H3a1 1 0 01-1-1V6a1 1 0 011-1h2m0 0V3a1 1 0 011-1h2a1 1 0 011 1v2m0 0h4m-6 4v10a1 1 0 001 1h4a1 1 0 001-1V11a1 1 0 00-1-1H9a1 1 0 00-1 1z"
                                            />
                                        </svg>
                                        <div>
                                            <h3 className="text-lg font-medium text-foreground">
                                                No events scheduled
                                            </h3>
                                            <p className="mt-1 text-muted-foreground">
                                                There are no upcoming track day
                                                events at the moment. Check back
                                                later!
                                            </p>
                                        </div>
                                        <Button
                                            variant="outline"
                                            className="mt-4"
                                        >
                                            Refresh Page
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </section>
                )}

                {/* Footer */}
                <footer className="border-t border-border bg-muted/30">
                    <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                        <div className="space-y-4 text-center">
                            <div className="flex items-center justify-center space-x-2">
                                <svg
                                    className="h-6 w-6 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                    />
                                </svg>
                                <span className="font-semibold text-foreground">
                                    Track Day Finder
                                </span>
                            </div>
                            <p className="text-sm text-muted-foreground">
                                &copy; 2024 Track Day Finder. Find your perfect
                                track day experience.
                            </p>
                            <div className="flex items-center justify-center space-x-6 text-sm">
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    About
                                </a>
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Contact
                                </a>
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Privacy
                                </a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
