import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import {
    Building,
    Calendar,
    ChevronLeft,
    ChevronRight,
    Clock,
    ExternalLink,
    MapPin,
} from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

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

interface EventsCalendarProps {
    events: Event[];
    filteredTrackId?: number | null;
    onClearFilter?: () => void;
}

export default function EventsCalendar({
    events,
    filteredTrackId,
    onClearFilter,
}: EventsCalendarProps) {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [selectedEvent, setSelectedEvent] = useState<Event | null>(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const calendarRef = useRef<HTMLDivElement>(null);

    // Filter events by track if filteredTrackId is provided
    const displayEvents = filteredTrackId
        ? events.filter((event) => event.track.id === filteredTrackId)
        : events;

    // Scroll to calendar when track is filtered
    useEffect(() => {
        if (filteredTrackId && calendarRef.current) {
            calendarRef.current.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }
    }, [filteredTrackId]);

    const filteredTrack = filteredTrackId
        ? events.find((event) => event.track.id === filteredTrackId)?.track
        : null;

    // Get the first day of the month and calculate calendar grid
    const firstDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1,
    );
    const startDate = new Date(firstDayOfMonth);
    startDate.setDate(startDate.getDate() - firstDayOfMonth.getDay());

    // Generate calendar days
    const calendarDays = [];
    const currentCalendarDate = new Date(startDate);

    for (let i = 0; i < 42; i++) {
        // 6 weeks * 7 days
        calendarDays.push(new Date(currentCalendarDate));
        currentCalendarDate.setDate(currentCalendarDate.getDate() + 1);
    }

    // Group events by date
    const eventsByDate = displayEvents.reduce(
        (acc, event) => {
            const eventDate = new Date(event.start_date).toDateString();
            if (!acc[eventDate]) {
                acc[eventDate] = [];
            }
            acc[eventDate].push(event);
            return acc;
        },
        {} as Record<string, Event[]>,
    );

    const navigateMonth = (direction: 'prev' | 'next') => {
        setCurrentDate((prev) => {
            const newDate = new Date(prev);
            newDate.setMonth(prev.getMonth() + (direction === 'next' ? 1 : -1));
            return newDate;
        });
    };

    const handleEventClick = (event: Event) => {
        setSelectedEvent(event);
        setIsDialogOpen(true);
    };

    const isToday = (date: Date) => {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    };

    const isCurrentMonth = (date: Date) => {
        return date.getMonth() === currentDate.getMonth();
    };

    const monthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    return (
        <div className="space-y-4 lg:space-y-8">
            <div
                ref={calendarRef}
                className="flex flex-col gap-4 lg:grid lg:grid-cols-3 lg:gap-8"
            >
                {/* Calendar */}
                <div className="order-1 lg:order-1 lg:col-span-2">
                    <Card>
                        <CardHeader className="p-4 lg:p-6">
                            <CardTitle className="space-y-3 lg:space-y-0">
                                {/* Mobile: Stack vertically, Desktop: Side by side */}
                                <div className="flex flex-col space-y-2 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
                                    <div className="flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4">
                                        <span className="text-lg font-semibold lg:text-xl">
                                            Calendar
                                        </span>
                                        {filteredTrack ? (
                                            <div className="flex flex-wrap items-center gap-2">
                                                <Badge
                                                    variant="default"
                                                    className="gap-1 text-xs"
                                                >
                                                    <MapPin className="h-3 w-3" />
                                                    <span className="max-w-[120px] truncate sm:max-w-none">
                                                        {filteredTrack.name}
                                                    </span>
                                                </Badge>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={onClearFilter}
                                                    className="h-7 px-2 text-xs"
                                                >
                                                    Clear
                                                </Button>
                                            </div>
                                        ) : (
                                            <Badge
                                                variant="secondary"
                                                className="w-fit text-xs"
                                            >
                                                {events.length} events
                                            </Badge>
                                        )}
                                    </div>

                                    {/* Month Navigation */}
                                    <div className="flex items-center justify-center space-x-2 lg:justify-end">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                navigateMonth('prev')
                                            }
                                        >
                                            <ChevronLeft className="h-4 w-4" />
                                        </Button>
                                        <span className="min-w-[120px] text-center text-base font-semibold sm:min-w-[140px] lg:text-lg">
                                            {monthNames[currentDate.getMonth()]}{' '}
                                            {currentDate.getFullYear()}
                                        </span>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                navigateMonth('next')
                                            }
                                        >
                                            <ChevronRight className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <div className="overflow-hidden">
                                {/* Day Names Header */}
                                <div className="grid grid-cols-7 border-t border-b bg-muted/30">
                                    {dayNames.map((day) => (
                                        <div
                                            key={day}
                                            className="border-r p-2 text-center text-xs font-medium text-muted-foreground last:border-r-0 sm:p-3 sm:text-sm"
                                        >
                                            <span className="sm:hidden">
                                                {day.slice(0, 1)}
                                            </span>
                                            <span className="hidden sm:inline">
                                                {day}
                                            </span>
                                        </div>
                                    ))}
                                </div>

                                {/* Calendar Grid */}
                                <div className="grid grid-cols-7">
                                    {calendarDays.map((date, index) => {
                                        const dateString = date.toDateString();
                                        const dayEvents =
                                            eventsByDate[dateString] || [];
                                        const isCurrentMonthDay =
                                            isCurrentMonth(date);
                                        const isTodayDate = isToday(date);

                                        return (
                                            <div
                                                key={index}
                                                className={`h-16 border-r p-1 last:border-r-0 sm:h-20 sm:p-2 ${
                                                    !isCurrentMonthDay
                                                        ? 'bg-muted/20 text-muted-foreground'
                                                        : ''
                                                } ${isTodayDate ? 'bg-primary/5' : ''} ${index < 35 ? 'border-b' : ''}`}
                                            >
                                                <div
                                                    className={`mb-1 text-xs font-medium sm:text-sm ${isTodayDate ? 'text-primary' : 'text-foreground'}`}
                                                >
                                                    {date.getDate()}
                                                </div>
                                                <div className="space-y-0.5 sm:space-y-1">
                                                    {dayEvents
                                                        .slice(
                                                            0,
                                                            isCurrentMonthDay
                                                                ? 1
                                                                : 0,
                                                        )
                                                        .map((event) => (
                                                            <div
                                                                key={event.id}
                                                                onClick={() =>
                                                                    handleEventClick(
                                                                        event,
                                                                    )
                                                                }
                                                                className="cursor-pointer truncate rounded bg-primary px-1 py-0.5 text-xs text-primary-foreground hover:bg-primary/90 sm:block"
                                                                title={
                                                                    event.title
                                                                }
                                                            >
                                                                <span className="sm:hidden">
                                                                    •
                                                                </span>
                                                                <span className="hidden sm:inline">
                                                                    {
                                                                        event.title
                                                                    }
                                                                </span>
                                                            </div>
                                                        ))}
                                                    <div className="hidden sm:block">
                                                        {dayEvents
                                                            .slice(1, 2)
                                                            .map((event) => (
                                                                <div
                                                                    key={
                                                                        event.id
                                                                    }
                                                                    onClick={() =>
                                                                        handleEventClick(
                                                                            event,
                                                                        )
                                                                    }
                                                                    className="cursor-pointer truncate rounded bg-primary px-1 py-0.5 text-xs text-primary-foreground hover:bg-primary/90"
                                                                    title={
                                                                        event.title
                                                                    }
                                                                >
                                                                    {
                                                                        event.title
                                                                    }
                                                                </div>
                                                            ))}
                                                    </div>
                                                    {dayEvents.length > 2 &&
                                                        isCurrentMonthDay && (
                                                            <div className="hidden text-xs text-muted-foreground sm:block">
                                                                +
                                                                {dayEvents.length -
                                                                    2}{' '}
                                                                more
                                                            </div>
                                                        )}
                                                    {dayEvents.length > 1 &&
                                                        isCurrentMonthDay && (
                                                            <div className="text-xs text-muted-foreground sm:hidden">
                                                                +
                                                                {dayEvents.length -
                                                                    1}
                                                            </div>
                                                        )}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Events List */}
                <div className="order-2 lg:order-2 lg:col-span-1">
                    <Card>
                        <CardHeader className="p-4 lg:p-6">
                            <CardTitle className="flex items-center justify-between">
                                <span className="text-lg font-semibold lg:text-xl">
                                    Events
                                </span>
                                <Badge variant="secondary" className="text-xs">
                                    {displayEvents.length} event
                                    {displayEvents.length !== 1 ? 's' : ''}
                                </Badge>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <ScrollArea className="h-[300px] sm:h-[400px] lg:h-[525px]">
                                <div className="divide-y border-t border-b">
                                    {displayEvents.map((event) => (
                                        <div
                                            key={event.id}
                                            onClick={() =>
                                                handleEventClick(event)
                                            }
                                            className="cursor-pointer p-3 transition-colors hover:bg-muted/50 sm:p-4"
                                        >
                                            <div className="space-y-2">
                                                <div className="flex flex-col space-y-1 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                                                    <h4 className="text-sm font-semibold">
                                                        {event.title}
                                                    </h4>
                                                    <Badge
                                                        variant="outline"
                                                        className="w-fit text-xs"
                                                    >
                                                        {new Date(
                                                            event.start_date,
                                                        ).toLocaleDateString()}
                                                    </Badge>
                                                </div>

                                                <div className="space-y-1 text-xs text-muted-foreground">
                                                    <div className="flex items-center gap-1">
                                                        <MapPin className="h-3 w-3 flex-shrink-0" />
                                                        <span className="truncate">
                                                            {event.track.name}
                                                        </span>
                                                    </div>
                                                    <div className="flex items-center gap-1">
                                                        <Building className="h-3 w-3 flex-shrink-0" />
                                                        <span className="truncate">
                                                            {
                                                                event.organizer
                                                                    .name
                                                            }
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}

                                    {displayEvents.length === 0 && (
                                        <div className="p-6 text-center sm:p-8">
                                            <div className="space-y-3">
                                                <Calendar className="mx-auto h-8 w-8 text-muted-foreground" />
                                                <div>
                                                    <h3 className="font-medium text-foreground">
                                                        No Events
                                                    </h3>
                                                    <p className="mt-1 text-sm text-muted-foreground">
                                                        {filteredTrackId
                                                            ? 'No events found for this track.'
                                                            : 'No events scheduled.'}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </ScrollArea>
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Event Details Dialog */}
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogContent className="top-1/2 left-1/2 max-w-sm -translate-x-1/2 -translate-y-1/2 transform rounded-lg sm:max-w-md">
                    <DialogHeader className="pb-2">
                        <DialogTitle className="text-base sm:text-lg">
                            Event Details
                        </DialogTitle>
                    </DialogHeader>

                    {selectedEvent && (
                        <div className="space-y-3 sm:space-y-4">
                            <div>
                                <h3 className="mb-1 text-base font-semibold sm:mb-2 sm:text-lg">
                                    {selectedEvent.title}
                                </h3>
                                <div className="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <Badge
                                        variant="secondary"
                                        className="gap-1 text-xs"
                                    >
                                        <Calendar className="h-3 w-3" />
                                        {new Date(
                                            selectedEvent.start_date,
                                        ).toLocaleDateString()}
                                    </Badge>
                                    {selectedEvent.start_date !==
                                        selectedEvent.end_date && (
                                        <Badge
                                            variant="outline"
                                            className="gap-1 text-xs"
                                        >
                                            <Clock className="h-3 w-3" />
                                            Multi-day
                                        </Badge>
                                    )}
                                </div>
                            </div>

                            <Separator />

                            <div className="space-y-2.5 sm:space-y-3">
                                <div className="flex items-start gap-2.5">
                                    <MapPin className="mt-0.5 h-4 w-4 flex-shrink-0 text-muted-foreground sm:h-5 sm:w-5" />
                                    <div className="min-w-0 flex-1">
                                        <div className="text-sm font-medium sm:text-base">
                                            {selectedEvent.track.name}
                                        </div>
                                        <div className="text-xs text-muted-foreground sm:text-sm">
                                            {selectedEvent.track.city},{' '}
                                            {selectedEvent.track.country}
                                        </div>
                                        <div className="mt-0.5 text-xs text-muted-foreground sm:mt-1">
                                            {selectedEvent.track.latitude.toFixed(
                                                4,
                                            )}
                                            ,{' '}
                                            {selectedEvent.track.longitude.toFixed(
                                                4,
                                            )}
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-start gap-2.5">
                                    <Building className="mt-0.5 h-4 w-4 flex-shrink-0 text-muted-foreground sm:h-5 sm:w-5" />
                                    <div className="min-w-0 flex-1">
                                        <div className="text-sm font-medium sm:text-base">
                                            {selectedEvent.organizer.name}
                                        </div>
                                        <div className="text-xs text-muted-foreground sm:text-sm">
                                            Event Organizer
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {selectedEvent.website && (
                                <>
                                    <Separator />
                                    <Button
                                        asChild
                                        className="w-full"
                                        size="sm"
                                    >
                                        <a
                                            href={selectedEvent.website}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="flex items-center gap-2"
                                        >
                                            <ExternalLink className="h-4 w-4" />
                                            <span className="text-sm">
                                                Visit Website
                                            </span>
                                        </a>
                                    </Button>
                                </>
                            )}
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </div>
    );
}
