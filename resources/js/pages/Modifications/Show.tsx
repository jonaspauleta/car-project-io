import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import cars from '@/routes/cars';
import { type BreadcrumbItem, type Car, type Modification } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, Calendar, Edit, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface ModificationShowProps {
    car: Car;
    modification: Modification;
}

const getBreadcrumbs = (car: Car, modification: Modification): BreadcrumbItem[] => [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Cars',
        href: '/cars',
    },
    {
        title: car.nickname || `${car.make} ${car.model}`,
        href: `/cars/${car.id}`,
    },
    {
        title: 'Modifications',
        href: `/cars/${car.id}/modifications`,
    },
    {
        title: modification.name,
        href: `/cars/${car.id}/modifications/${modification.id}`,
    },
];

export default function ModificationShow({ car, modification }: ModificationShowProps) {
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const handleDelete = () => {
        router.delete(cars.modifications.destroy.url({ car, modification }), {
            onSuccess: () => {
                // Redirect will be handled by the controller
            },
        });
    };

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
        <AppLayout breadcrumbs={getBreadcrumbs(car, modification)}>
            <Head title={`${modification.name} - ${car.nickname || `${car.make} ${car.model}`}`} />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={cars.modifications.index.url({ car })}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Modifications
                            </Button>
                        </Link>
                        <div>
                            <div className="mb-1 flex items-center gap-2">
                                <h1 className="text-3xl font-bold tracking-tight">{modification.name}</h1>
                                <Badge variant={modification.is_active ? 'default' : 'secondary'}>
                                    {modification.is_active ? 'Active' : 'Inactive'}
                                </Badge>
                            </div>
                            <p className="text-muted-foreground">
                                {modification.category} • {car.make} {car.model} ({car.year})
                            </p>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Link href={cars.modifications.edit.url({ car, modification })}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                        </Link>
                        <Button variant="destructive" onClick={() => setShowDeleteConfirm(true)}>
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Modification Details */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Modification Details</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div>
                                    <h4 className="text-sm font-medium text-muted-foreground">Name</h4>
                                    <p className="text-lg">{modification.name}</p>
                                </div>

                                <div>
                                    <h4 className="text-sm font-medium text-muted-foreground">Category</h4>
                                    <p className="text-lg">{modification.category}</p>
                                </div>

                                {modification.brand && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Brand</h4>
                                        <p className="text-lg">{modification.brand}</p>
                                    </div>
                                )}

                                {modification.vendor && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Vendor</h4>
                                        <p className="text-lg">{modification.vendor}</p>
                                    </div>
                                )}

                                {modification.installation_date && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Installation Date</h4>
                                        <p className="text-lg">{formatDate(modification.installation_date)}</p>
                                    </div>
                                )}

                                {modification.cost && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Cost</h4>
                                        <p className="text-lg">{formatCurrency(modification.cost)}</p>
                                    </div>
                                )}

                                <div>
                                    <h4 className="text-sm font-medium text-muted-foreground">Status</h4>
                                    <Badge variant={modification.is_active ? 'default' : 'secondary'}>
                                        {modification.is_active ? 'Active' : 'Inactive'}
                                    </Badge>
                                </div>

                                {modification.notes && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Notes</h4>
                                        <p className="text-sm whitespace-pre-wrap">{modification.notes}</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Car Information */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Car Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <h4 className="text-sm font-medium text-muted-foreground">Car</h4>
                                    <p className="text-lg">
                                        {car.make} {car.model}
                                    </p>
                                </div>

                                <div>
                                    <h4 className="text-sm font-medium text-muted-foreground">Year</h4>
                                    <p className="text-lg">{car.year}</p>
                                </div>

                                {car.nickname && (
                                    <div>
                                        <h4 className="text-sm font-medium text-muted-foreground">Nickname</h4>
                                        <p className="text-lg">{car.nickname}</p>
                                    </div>
                                )}

                                <div className="pt-4">
                                    <Link href={cars.show.url(car)}>
                                        <Button variant="outline" className="w-full">
                                            View Car Details
                                        </Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Timeline */}
                        <Card className="mt-6">
                            <CardHeader>
                                <CardTitle>Timeline</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex items-center gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                        <Calendar className="h-4 w-4 text-primary" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium">Added to collection</p>
                                        <p className="text-xs text-muted-foreground">{formatDate(modification.created_at)}</p>
                                    </div>
                                </div>

                                {modification.installation_date && (
                                    <div className="flex items-center gap-3">
                                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                                            <Calendar className="h-4 w-4 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium">Installed</p>
                                            <p className="text-xs text-muted-foreground">{formatDate(modification.installation_date)}</p>
                                        </div>
                                    </div>
                                )}

                                <div className="flex items-center gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                                        <Calendar className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium">Last updated</p>
                                        <p className="text-xs text-muted-foreground">{formatDate(modification.updated_at)}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Delete Confirmation Dialog */}
                {showDeleteConfirm && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <Card className="mx-4 w-full max-w-md">
                            <CardHeader>
                                <CardTitle>Delete Modification</CardTitle>
                                <CardDescription>
                                    Are you sure you want to delete "{modification.name}"? This action cannot be undone.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex justify-end gap-2">
                                    <Button variant="outline" onClick={() => setShowDeleteConfirm(false)}>
                                        Cancel
                                    </Button>
                                    <Button variant="destructive" onClick={handleDelete}>
                                        Delete Modification
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
