import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Car, type PaginatedResponse } from '@/types';
import cars from '@/routes/cars';
import carcontroller from '@/actions/App/Http/Controllers/CarController';
import Pagination from '@/components/ui/custom-pagination';
import { Head, Link, router } from '@inertiajs/react';
import { Car as CarIcon, Plus, Search, Calendar, Wrench, Filter, X } from 'lucide-react';
import { useState } from 'react';

interface CarsIndexProps {
    cars: PaginatedResponse<Car>;
    filters: {
        search?: string;
        make?: string;
        model?: string;
        year?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Cars',
        href: '/cars',
    },
];

export default function CarsIndex({ cars: carsData, filters }: CarsIndexProps) {
    const [search, setSearch] = useState(filters.search || '');
    const [make, setMake] = useState(filters.make || 'all');
    const [model, setModel] = useState(filters.model || 'all');
    const [year, setYear] = useState(filters.year || 'all');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();

        const params: Record<string, string | undefined> = {};
        if (search) params.search = search;
        if (make && make !== 'all') params['filter[make]'] = make;
        if (model && model !== 'all') params['filter[model]'] = model;
        if (year && year !== 'all') params['filter[year]'] = year;

        router.get(cars.index.url(), params, {
            preserveState: true,
            replace: true,
        });
    };

    const clearFilters = () => {
        setSearch('');
        setMake('all');
        setModel('all');
        setYear('all');
        router.get(cars.index.url(), {}, {
            preserveState: true,
            replace: true,
        });
    };

    const hasActiveFilters = search || (make && make !== 'all') || (model && model !== 'all') || (year && year !== 'all');

    // Generate unique makes, models, and years from the data
    const uniqueMakes = [...new Set(carsData.data.map(car => car.make))].sort();
    const uniqueModels = [...new Set(carsData.data.map(car => car.model))].sort();
    const uniqueYears = [...new Set(carsData.data.map(car => car.year))].sort((a, b) => b - a);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Cars" />
            
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">My Cars</h1>
                        <p className="text-muted-foreground">
                            Manage your car collection and modifications
                        </p>
                    </div>
                    <Link href={cars.create.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Car
                        </Button>
                    </Link>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle className="flex items-center gap-2">
                                    <Filter className="h-5 w-5" />
                                    Search & Filter
                                </CardTitle>
                                <CardDescription>
                                    Find cars by make, model, year, or nickname
                                </CardDescription>
                            </div>
                            {hasActiveFilters && (
                                <Button type="button" variant="ghost" size="sm" onClick={clearFilters}>
                                    <X className="h-4 w-4 mr-1" />
                                    Clear All
                                </Button>
                            )}
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSearch} className="space-y-4">
                            {/* Search Input */}
                            <div className="relative">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    placeholder="Search cars by nickname, make, model, or VIN..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="pl-10"
                                />
                            </div>

                            {/* Filter Controls */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {/* Make Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="make-filter">Make</Label>
                                    <Select value={make} onValueChange={setMake}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="All makes" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All makes</SelectItem>
                                            {uniqueMakes.map((makeOption) => (
                                                <SelectItem key={makeOption} value={makeOption}>
                                                    {makeOption}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Model Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="model-filter">Model</Label>
                                    <Select value={model} onValueChange={setModel}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="All models" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All models</SelectItem>
                                            {uniqueModels.map((modelOption) => (
                                                <SelectItem key={modelOption} value={modelOption}>
                                                    {modelOption}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Year Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="year-filter">Year</Label>
                                    <Select value={year} onValueChange={setYear}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="All years" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">All years</SelectItem>
                                            {uniqueYears.map((yearOption) => (
                                                <SelectItem key={yearOption} value={yearOption.toString()}>
                                                    {yearOption}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex gap-2">
                                <Button type="submit" variant="outline">
                                    <Search className="h-4 w-4 mr-2" />
                                    Apply Filters
                                </Button>
                                {hasActiveFilters && (
                                    <Button type="button" variant="ghost" onClick={clearFilters}>
                                        <X className="h-4 w-4 mr-2" />
                                        Clear Filters
                                    </Button>
                                )}
                            </div>

                            {/* Active Filters Display */}
                            {hasActiveFilters && (
                                <div className="flex flex-wrap gap-2 pt-2 border-t">
                                    <span className="text-sm text-muted-foreground">Active filters:</span>
                                    {search && (
                                        <Badge variant="secondary" className="gap-1">
                                            Search: {search}
                                            <button
                                                type="button"
                                                onClick={() => setSearch('')}
                                                className="ml-1 hover:bg-muted-foreground/20 rounded-full p-0.5"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {make && make !== 'all' && (
                                        <Badge variant="secondary" className="gap-1">
                                            Make: {make}
                                            <button
                                                type="button"
                                                onClick={() => setMake('all')}
                                                className="ml-1 hover:bg-muted-foreground/20 rounded-full p-0.5"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {model && model !== 'all' && (
                                        <Badge variant="secondary" className="gap-1">
                                            Model: {model}
                                            <button
                                                type="button"
                                                onClick={() => setModel('all')}
                                                className="ml-1 hover:bg-muted-foreground/20 rounded-full p-0.5"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {year && year !== 'all' && (
                                        <Badge variant="secondary" className="gap-1">
                                            Year: {year}
                                            <button
                                                type="button"
                                                onClick={() => setYear('all')}
                                                className="ml-1 hover:bg-muted-foreground/20 rounded-full p-0.5"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                </div>
                            )}
                        </form>
                    </CardContent>
                </Card>

                {/* Cars Grid */}
                {carsData.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <CarIcon className="h-12 w-12 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold mb-2">No cars found</h3>
                            <p className="text-muted-foreground text-center mb-4">
                                {hasActiveFilters
                                    ? 'Try adjusting your search criteria'
                                    : 'Get started by adding your first car'}
                            </p>
                            <Link href={cars.create.url()}>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add Your First Car
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {carsData.data.map((car) => (
                            <Card key={car.id} className="hover:shadow-md transition-shadow">
                                <CardHeader>
                                    <div className="flex items-start justify-between">
                                        <div>
                                            <CardTitle className="text-xl">
                                                {car.nickname || `${car.make} ${car.model}`}
                                            </CardTitle>
                                            <CardDescription>
                                                {car.make} {car.model} ({car.year})
                                            </CardDescription>
                                        </div>
                                        <Badge variant="secondary">
                                            {car.modifications?.length || 0} mods
                                        </Badge>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {car.vin && (
                                            <div className="flex items-center text-sm text-muted-foreground">
                                                <span className="font-medium">VIN:</span>
                                                <span className="ml-2 font-mono">{car.vin}</span>
                                            </div>
                                        )}
                                        
                                        {car.notes && (
                                            <p className="text-sm text-muted-foreground line-clamp-2">
                                                {car.notes}
                                            </p>
                                        )}

                                        <div className="flex items-center justify-between pt-2">
                                            <div className="flex items-center text-sm text-muted-foreground">
                                                <Calendar className="h-4 w-4 mr-1" />
                                                Added {new Date(car.created_at).toLocaleDateString()}
                                            </div>
                                            <div className="flex gap-2">
                                                <Link href={cars.show.url(car)}>
                                                    <Button variant="outline" size="sm">
                                                        View
                                                    </Button>
                                                </Link>
                                                <Link href={cars.edit.url(car)}>
                                                    <Button variant="ghost" size="sm">
                                                        Edit
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {/* Pagination */}
                <Pagination pagination={carsData} />
            </div>
        </AppLayout>
    );
}
