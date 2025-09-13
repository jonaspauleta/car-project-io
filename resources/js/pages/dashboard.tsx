import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import cars from '@/routes/cars';
import { type BreadcrumbItem, type Car, type Modification } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Award, BarChart3, Car as CarIcon, DollarSign, Plus, TrendingUp, Wrench } from 'lucide-react';

interface DashboardStats {
    totalCars: number;
    totalModifications: number;
    activeModifications: number;
    totalSpent: number;
}

interface DashboardProps {
    stats: DashboardStats;
    recentCars: Car[];
    recentModifications: Modification[];
    carsWithMostModifications: Car[];
    modificationCategories: Array<{
        category: string;
        count: number;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({ stats, recentCars, recentModifications, carsWithMostModifications, modificationCategories }: DashboardProps) {
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
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground">Welcome to your car collection management system</p>
                    </div>
                    <Link href={cars.create.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Car
                        </Button>
                    </Link>
                </div>

                {/* Statistics Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Cars</CardTitle>
                            <CarIcon className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalCars}</div>
                            <p className="text-xs text-muted-foreground">Cars in your collection</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Modifications</CardTitle>
                            <Wrench className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalModifications}</div>
                            <p className="text-xs text-muted-foreground">{stats.activeModifications} active modifications</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Spent</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalSpent)}</div>
                            <p className="text-xs text-muted-foreground">On modifications</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Activity Level</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {stats.totalCars > 0 ? Math.round((stats.totalModifications / stats.totalCars) * 10) / 10 : 0}
                            </div>
                            <p className="text-xs text-muted-foreground">Mods per car</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Recent Cars */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <div>
                                    <CardTitle>Recent Cars</CardTitle>
                                    <CardDescription>Your latest additions to the collection</CardDescription>
                                </div>
                                <Link href={cars.index.url()}>
                                    <Button variant="outline" size="sm">
                                        View All
                                    </Button>
                                </Link>
                            </div>
                        </CardHeader>
                        <CardContent>
                            {recentCars.length === 0 ? (
                                <div className="py-6 text-center">
                                    <CarIcon className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                                    <h3 className="mb-2 text-lg font-semibold">No cars yet</h3>
                                    <p className="mb-4 text-muted-foreground">Start building your collection by adding your first car</p>
                                    <Link href={cars.create.url()}>
                                        <Button>
                                            <Plus className="mr-2 h-4 w-4" />
                                            Add Your First Car
                                        </Button>
                                    </Link>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {recentCars.map((car) => (
                                        <div key={car.id} className="flex items-center justify-between rounded-lg border p-4">
                                            <div className="flex flex-1 items-center gap-4">
                                                {/* Car Image */}
                                                {car.image_url && (
                                                    <div className="relative h-16 w-24 flex-shrink-0 overflow-hidden rounded-md">
                                                        <img
                                                            src={car.image_url}
                                                            alt={car.nickname || `${car.make} ${car.model}`}
                                                            className="h-full w-full object-cover"
                                                            onError={(e) => {
                                                                const target = e.target as HTMLImageElement;
                                                                target.style.display = 'none';
                                                            }}
                                                        />
                                                    </div>
                                                )}
                                                <div className="flex-1">
                                                    <h4 className="font-semibold">{car.nickname || `${car.make} ${car.model}`}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {car.make} {car.model} ({car.year})
                                                    </p>
                                                    <p className="text-xs text-muted-foreground">Added {formatDate(car.created_at)}</p>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Badge variant="secondary">{car.modifications_count || 0} mods</Badge>
                                                <Link href={cars.show.url({ car })}>
                                                    <Button variant="ghost" size="sm">
                                                        View
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Recent Modifications */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Modifications</CardTitle>
                            <CardDescription>Latest modifications added to your cars</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {recentModifications.length === 0 ? (
                                <div className="py-6 text-center">
                                    <Wrench className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                                    <h3 className="mb-2 text-lg font-semibold">No modifications yet</h3>
                                    <p className="mb-4 text-muted-foreground">Start customizing your cars with modifications</p>
                                    {stats.totalCars > 0 && (
                                        <Link href={cars.index.url()}>
                                            <Button>
                                                <Plus className="mr-2 h-4 w-4" />
                                                Add Modification
                                            </Button>
                                        </Link>
                                    )}
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {recentModifications.map((modification) => (
                                        <div key={modification.id} className="flex items-center justify-between rounded-lg border p-4">
                                            <div className="flex-1">
                                                <h4 className="font-semibold">{modification.name}</h4>
                                                <p className="text-sm text-muted-foreground">
                                                    {modification.car?.nickname || `${modification.car?.make} ${modification.car?.model}`}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    {modification.category} • Added {formatDate(modification.created_at)}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Badge variant={modification.is_active ? 'default' : 'secondary'}>
                                                    {modification.is_active ? 'Active' : 'Inactive'}
                                                </Badge>
                                                {modification.cost && (
                                                    <span className="text-sm font-medium">{formatCurrency(modification.cost)}</span>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Cars with Most Modifications */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Award className="h-5 w-5" />
                                <CardTitle>Most Modified Cars</CardTitle>
                            </div>
                            <CardDescription>Your cars with the most modifications</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {carsWithMostModifications.length === 0 ? (
                                <div className="py-6 text-center">
                                    <CarIcon className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                                    <p className="text-muted-foreground">No cars with modifications yet</p>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {carsWithMostModifications.map((car, index) => (
                                        <div key={car.id} className="flex items-center justify-between rounded-lg border p-4">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                                    <span className="text-sm font-bold text-primary">{index + 1}</span>
                                                </div>
                                                <div className="flex-1">
                                                    <h4 className="font-semibold">{car.nickname || `${car.make} ${car.model}`}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {car.make} {car.model} ({car.year})
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <Badge variant="default">{car.modifications_count || 0} mods</Badge>
                                                <Link href={cars.show.url({ car })}>
                                                    <Button variant="ghost" size="sm">
                                                        View
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Modification Categories */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <BarChart3 className="h-5 w-5" />
                                <CardTitle>Modification Categories</CardTitle>
                            </div>
                            <CardDescription>Breakdown of your modifications by category</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {modificationCategories.length === 0 ? (
                                <div className="py-6 text-center">
                                    <Wrench className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                                    <p className="text-muted-foreground">No modifications to categorize yet</p>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    {modificationCategories.map((category, index) => (
                                        <div key={category.category} className="flex items-center justify-between rounded-lg border p-4">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-muted">
                                                    <span className="text-sm font-bold">{index + 1}</span>
                                                </div>
                                                <div>
                                                    <h4 className="font-semibold">{category.category}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {category.count} modification{category.count !== 1 ? 's' : ''}
                                                    </p>
                                                </div>
                                            </div>
                                            <Badge variant="outline">{category.count}</Badge>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
