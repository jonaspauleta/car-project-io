import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import Pagination from '@/components/ui/custom-pagination';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import cars from '@/routes/cars';
import {
    type BreadcrumbItem,
    type Car,
    type Modification,
    type PaginatedResponse,
} from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowLeft,
    Calendar,
    DollarSign,
    Filter,
    Plus,
    Search,
    Wrench,
    X,
} from 'lucide-react';
import { useState } from 'react';

interface ModificationsIndexProps {
    car: Car;
    modifications: PaginatedResponse<Modification>;
    filters: {
        search?: string;
        name?: string;
        category?: string;
        brand?: string;
        vendor?: string;
        is_active?: string;
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

export default function ModificationsIndex({
    car,
    modifications,
    filters,
}: ModificationsIndexProps) {
    const [search, setSearch] = useState(filters.search || '');
    const [name, setName] = useState(filters.name || 'all');
    const [category, setCategory] = useState(filters.category || 'all');
    const [brand, setBrand] = useState(filters.brand || 'all');
    const [vendor, setVendor] = useState(filters.vendor || 'all');
    const [isActive, setIsActive] = useState(filters.is_active || 'all');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();

        const params: Record<string, string | undefined> = {};
        if (search) params.search = search;
        if (name && name !== 'all') params['filter[name]'] = name;
        if (category && category !== 'all')
            params['filter[category]'] = category;
        if (brand && brand !== 'all') params['filter[brand]'] = brand;
        if (vendor && vendor !== 'all') params['filter[vendor]'] = vendor;
        if (isActive && isActive !== 'all')
            params['filter[is_active]'] = isActive;

        router.get(cars.modifications.index.url({ car }), params, {
            preserveState: true,
            replace: true,
        });
    };

    const clearFilters = () => {
        setSearch('');
        setName('all');
        setCategory('all');
        setBrand('all');
        setVendor('all');
        setIsActive('all');
        router.get(
            cars.modifications.index.url({ car }),
            {},
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const hasActiveFilters =
        search ||
        (name && name !== 'all') ||
        (category && category !== 'all') ||
        (brand && brand !== 'all') ||
        (vendor && vendor !== 'all') ||
        (isActive && isActive !== 'all');

    // Generate unique values from the data
    const uniqueNames = [
        ...new Set(modifications.data.map((mod) => mod.name)),
    ].sort();
    const uniqueCategories = [
        ...new Set(modifications.data.map((mod) => mod.category)),
    ].sort();
    const uniqueBrands = [
        ...new Set(modifications.data.map((mod) => mod.brand).filter(Boolean)),
    ].sort();
    const uniqueVendors = [
        ...new Set(modifications.data.map((mod) => mod.vendor).filter(Boolean)),
    ].sort();

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString();
    };

    return (
        <AppLayout
            breadcrumbs={[
                ...breadcrumbs,
                {
                    title: car.nickname || `${car.make} ${car.model}`,
                    href: `/cars/${car.id}`,
                },
                {
                    title: 'Modifications',
                    href: `/cars/${car.id}/modifications`,
                },
            ]}
        >
            <Head
                title={`Modifications - ${car.nickname || `${car.make} ${car.model}`}`}
            />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={cars.show.url(car)}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Car
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">
                                Modifications
                            </h1>
                            <p className="text-muted-foreground">
                                {car.make} {car.model} ({car.year})
                            </p>
                        </div>
                    </div>
                    <Link href={cars.modifications.create.url({ car })}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Modification
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
                                    Find modifications by name, category, brand,
                                    vendor, or status
                                </CardDescription>
                            </div>
                            {hasActiveFilters && (
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    onClick={clearFilters}
                                >
                                    <X className="mr-1 h-4 w-4" />
                                    Clear All
                                </Button>
                            )}
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSearch} className="space-y-4">
                            {/* Search Input */}
                            <div className="relative">
                                <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    placeholder="Search modifications by name, notes, or description..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="pl-10"
                                />
                            </div>

                            {/* Filter Controls */}
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {/* Name Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="name-filter">Name</Label>
                                    <Select
                                        value={name}
                                        onValueChange={setName}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All names" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                All names
                                            </SelectItem>
                                            {uniqueNames.map((nameOption) => (
                                                <SelectItem
                                                    key={nameOption}
                                                    value={nameOption}
                                                >
                                                    {nameOption}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Category Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="category-filter">
                                        Category
                                    </Label>
                                    <Select
                                        value={category}
                                        onValueChange={setCategory}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All categories" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                All categories
                                            </SelectItem>
                                            {uniqueCategories.map(
                                                (categoryOption) => (
                                                    <SelectItem
                                                        key={categoryOption}
                                                        value={categoryOption}
                                                    >
                                                        {categoryOption}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Brand Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="brand-filter">Brand</Label>
                                    <Select
                                        value={brand}
                                        onValueChange={setBrand}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All brands" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                All brands
                                            </SelectItem>
                                            {uniqueBrands.map((brandOption) => (
                                                <SelectItem
                                                    key={brandOption}
                                                    value={brandOption!}
                                                >
                                                    {brandOption}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Vendor Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="vendor-filter">
                                        Vendor
                                    </Label>
                                    <Select
                                        value={vendor}
                                        onValueChange={setVendor}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All vendors" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                All vendors
                                            </SelectItem>
                                            {uniqueVendors.map(
                                                (vendorOption) => (
                                                    <SelectItem
                                                        key={vendorOption}
                                                        value={vendorOption!}
                                                    >
                                                        {vendorOption}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Status Filter */}
                                <div className="space-y-2">
                                    <Label htmlFor="status-filter">
                                        Status
                                    </Label>
                                    <Select
                                        value={isActive}
                                        onValueChange={setIsActive}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="All statuses" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                All statuses
                                            </SelectItem>
                                            <SelectItem value="1">
                                                Active
                                            </SelectItem>
                                            <SelectItem value="0">
                                                Inactive
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex gap-2">
                                <Button type="submit" variant="outline">
                                    <Search className="mr-2 h-4 w-4" />
                                    Apply Filters
                                </Button>
                                {hasActiveFilters && (
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        onClick={clearFilters}
                                    >
                                        <X className="mr-2 h-4 w-4" />
                                        Clear Filters
                                    </Button>
                                )}
                            </div>

                            {/* Active Filters Display */}
                            {hasActiveFilters && (
                                <div className="flex flex-wrap gap-2 border-t pt-2">
                                    <span className="text-sm text-muted-foreground">
                                        Active filters:
                                    </span>
                                    {search && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Search: {search}
                                            <button
                                                type="button"
                                                onClick={() => setSearch('')}
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {name && name !== 'all' && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Name: {name}
                                            <button
                                                type="button"
                                                onClick={() => setName('all')}
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {category && category !== 'all' && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Category: {category}
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    setCategory('all')
                                                }
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {brand && brand !== 'all' && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Brand: {brand}
                                            <button
                                                type="button"
                                                onClick={() => setBrand('all')}
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {vendor && vendor !== 'all' && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Vendor: {vendor}
                                            <button
                                                type="button"
                                                onClick={() => setVendor('all')}
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </Badge>
                                    )}
                                    {isActive && isActive !== 'all' && (
                                        <Badge
                                            variant="secondary"
                                            className="gap-1"
                                        >
                                            Status:{' '}
                                            {isActive === '1'
                                                ? 'Active'
                                                : 'Inactive'}
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    setIsActive('all')
                                                }
                                                className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
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

                {/* Modifications List */}
                {modifications.data.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <Wrench className="mb-4 h-12 w-12 text-muted-foreground" />
                            <h3 className="mb-2 text-lg font-semibold">
                                No modifications found
                            </h3>
                            <p className="mb-4 text-center text-muted-foreground">
                                {hasActiveFilters
                                    ? 'Try adjusting your search criteria'
                                    : 'Get started by adding your first modification'}
                            </p>
                            <Link href={cars.modifications.create.url({ car })}>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add First Modification
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {modifications.data.map((modification) => (
                            <Card
                                key={modification.id}
                                className="transition-shadow hover:shadow-md"
                            >
                                <CardContent className="p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="mb-2 flex items-center gap-2">
                                                <h3 className="text-lg font-semibold">
                                                    {modification.name}
                                                </h3>
                                                <Badge
                                                    variant={
                                                        modification.is_active
                                                            ? 'default'
                                                            : 'secondary'
                                                    }
                                                >
                                                    {modification.is_active
                                                        ? 'Active'
                                                        : 'Inactive'}
                                                </Badge>
                                            </div>

                                            <p className="mb-3 text-muted-foreground">
                                                {modification.category}
                                                {modification.brand &&
                                                    ` • ${modification.brand}`}
                                                {modification.vendor &&
                                                    ` • ${modification.vendor}`}
                                            </p>

                                            {modification.notes && (
                                                <p className="mb-3 line-clamp-2 text-sm">
                                                    {modification.notes}
                                                </p>
                                            )}

                                            <div className="flex items-center gap-6 text-sm text-muted-foreground">
                                                {modification.installation_date && (
                                                    <div className="flex items-center gap-1">
                                                        <Calendar className="h-4 w-4" />
                                                        Installed{' '}
                                                        {formatDate(
                                                            modification.installation_date,
                                                        )}
                                                    </div>
                                                )}
                                                {modification.cost && (
                                                    <div className="flex items-center gap-1">
                                                        <DollarSign className="h-4 w-4" />
                                                        {formatCurrency(
                                                            modification.cost,
                                                        )}
                                                    </div>
                                                )}
                                                <div className="flex items-center gap-1">
                                                    <Calendar className="h-4 w-4" />
                                                    Added{' '}
                                                    {formatDate(
                                                        modification.created_at,
                                                    )}
                                                </div>
                                            </div>
                                        </div>

                                        <div className="flex gap-2">
                                            <Link
                                                href={cars.modifications.show.url(
                                                    {
                                                        car: car.id,
                                                        modification:
                                                            modification.id,
                                                    },
                                                )}
                                            >
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    View
                                                </Button>
                                            </Link>
                                            <Link
                                                href={cars.modifications.edit.url(
                                                    {
                                                        car: car.id,
                                                        modification:
                                                            modification.id,
                                                    },
                                                )}
                                            >
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                >
                                                    Edit
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {/* Pagination */}
                <Pagination pagination={modifications} />
            </div>
        </AppLayout>
    );
}
