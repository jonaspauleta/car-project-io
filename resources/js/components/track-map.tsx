import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Skeleton } from '@/components/ui/skeleton';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { Building, Calendar, MapPin } from 'lucide-react';
import { useEffect, useRef } from 'react';
import { createRoot } from 'react-dom/client';

// Create custom red marker icon to match Shadcn theme
const createCustomIcon = () => {
    // Create a custom red marker SVG
    const redMarkerSvg = `
        <svg viewBox="-3 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>pin_fill_sharp_circle [#634]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-223.000000, -5399.000000)" fill="#000000"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M174,5248.219 C172.895,5248.219 172,5247.324 172,5246.219 C172,5245.114 172.895,5244.219 174,5244.219 C175.105,5244.219 176,5245.114 176,5246.219 C176,5247.324 175.105,5248.219 174,5248.219 M174,5239 C170.134,5239 167,5242.134 167,5246 C167,5249.866 174,5259 174,5259 C174,5259 181,5249.866 181,5246 C181,5242.134 177.866,5239 174,5239" id="pin_fill_sharp_circle-[#634]"> </path> </g> </g> </g> </g></svg>
    `;

    const redMarkerRetinaUrl = 'data:image/svg+xml;base64,' + btoa(redMarkerSvg);
    const redMarkerUrl = 'data:image/svg+xml;base64,' + btoa(redMarkerSvg);

    return L.icon({
        iconUrl: redMarkerUrl,
        iconRetinaUrl: redMarkerRetinaUrl,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
        shadowAnchor: [12, 41],
    });
};

// Set the custom icon as default
const customIcon = createCustomIcon();

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

interface TrackMapProps {
    events: Event[];
    className?: string;
    isLoading?: boolean;
    onFilterTrack?: (trackId: number) => void;
}

// Popup component using Shadcn UI for track information
function TrackPopup({ track, eventCount, onFilterTrack }: { track: Event['track']; eventCount: number; onFilterTrack: (trackId: number) => void }) {
    return (
        <Card className="w-80 border-0 shadow-lg">
            <CardContent className="space-y-4 p-4">
                <div className="space-y-2">
                    <h3 className="text-lg leading-tight font-semibold">{track.name}</h3>
                    <Badge variant="secondary" className="gap-1">
                        <Calendar className="h-3 w-3" />
                        {eventCount} event{eventCount !== 1 ? 's' : ''}
                    </Badge>
                </div>

                <div className="space-y-3 text-sm">
                    <div className="flex items-start gap-2">
                        <MapPin className="mt-0.5 h-4 w-4 flex-shrink-0 text-muted-foreground" />
                        <div className="flex-1">
                            <div className="font-medium">
                                {track.city}, {track.country}
                            </div>
                            <div className="text-xs text-muted-foreground">
                                {track.latitude.toFixed(4)}, {track.longitude.toFixed(4)}
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Building className="h-4 w-4 flex-shrink-0 text-muted-foreground" />
                        <span className="text-muted-foreground">Racing Circuit</span>
                    </div>
                </div>

                <Separator />
                <Button onClick={() => onFilterTrack(track.id)} className="w-full gap-2" size="sm">
                    <Calendar className="h-4 w-4" />
                    View Events at This Track
                </Button>
            </CardContent>
        </Card>
    );
}

export default function TrackMap({ events, className = '', isLoading = false, onFilterTrack }: TrackMapProps) {
    const mapRef = useRef<HTMLDivElement>(null);
    const mapInstanceRef = useRef<L.Map | null>(null);

    useEffect(() => {
        if (!mapRef.current || mapInstanceRef.current || isLoading) return;

        // Initialize the map
        const map = L.map(mapRef.current, {
            center: [50.0, 10.0], // Center on Europe initially
            zoom: 5,
            zoomControl: true,
            attributionControl: true,
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true,
            preferCanvas: false,
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Group events by track to avoid duplicate markers
        const trackGroups = events.reduce(
            (acc, event) => {
                const trackKey = `${event.track.latitude}-${event.track.longitude}`;
                if (!acc[trackKey]) {
                    acc[trackKey] = {
                        track: event.track,
                        events: [],
                    };
                }
                acc[trackKey].events.push(event);
                return acc;
            },
            {} as Record<string, { track: Event['track']; events: Event[] }>,
        );

        // Add markers for tracks
        const bounds = L.latLngBounds([]);

        Object.values(trackGroups).forEach(({ track, events: trackEvents }) => {
            if (track.latitude && track.longitude) {
                const marker = L.marker([track.latitude, track.longitude], {
                    icon: customIcon,
                }).addTo(map);

                // Create a container div for the React component
                const popupContainer = document.createElement('div');

                // Pre-render the React component to get proper dimensions
                const root = createRoot(popupContainer);
                root.render(<TrackPopup track={track} eventCount={trackEvents.length} onFilterTrack={onFilterTrack || (() => {})} />);

                // Create popup with the container
                const popup = L.popup({
                    maxWidth: 320,
                    className: 'custom-popup',
                    autoPan: true,
                    autoPanPadding: [20, 20],
                    keepInView: true,
                    closeOnClick: true,
                    closeOnEscapeKey: true,
                }).setContent(popupContainer);

                marker.bindPopup(popup);

                bounds.extend([track.latitude, track.longitude]);
            }
        });

        // Fit map to show all markers if we have events
        if (events.length > 0) {
            map.fitBounds(bounds, { padding: [20, 20] });
        }

        mapInstanceRef.current = map;

        // Cleanup function
        return () => {
            if (mapInstanceRef.current) {
                mapInstanceRef.current.remove();
                mapInstanceRef.current = null;
            }
        };
    }, [events, isLoading, onFilterTrack]);

    if (isLoading) {
        return (
            <Card className={className}>
                <CardContent className="p-6">
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <Skeleton className="h-6 w-48" />
                            <Skeleton className="h-5 w-20" />
                        </div>
                        <Skeleton className="h-96 w-full" />
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card className={className}>
            <CardContent className="p-0">
                <div className="border-b border-border p-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="text-lg font-semibold text-foreground">Track Day Events Map</h3>
                            <p className="mt-1 text-sm text-muted-foreground">Click on any marker to see event details</p>
                        </div>
                        <Badge variant="secondary">
                            {events.length} event{events.length !== 1 ? 's' : ''}
                        </Badge>
                    </div>
                </div>
                <div className="relative overflow-hidden rounded-b-lg">
                    <div ref={mapRef} className="h-96 w-full transition-all duration-300" style={{ minHeight: '400px' }} />
                    {events.length === 0 && (
                        <div className="absolute inset-0 flex items-center justify-center bg-muted/50">
                            <div className="text-center">
                                <svg className="mx-auto h-12 w-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h3 className="mt-2 text-sm font-medium text-muted-foreground">No events found</h3>
                                <p className="mt-1 text-sm text-muted-foreground">There are no upcoming track day events to display.</p>
                            </div>
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
