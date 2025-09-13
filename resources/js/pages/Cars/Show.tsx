import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Car, type Modification } from '@/types';
import cars from '@/routes/cars';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, Calendar, DollarSign, Edit, Plus, Trash2, Wrench } from 'lucide-react';
import { useState } from 'react';

interface CarShowProps {
    car: Car;
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

export default function CarShow({ car }: CarShowProps) {
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const handleDelete = () => {
        router.delete(cars.destroy.url(car), {
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
        <AppLayout breadcrumbs={[...breadcrumbs, { title: car.nickname || `${car.make} ${car.model}`, href: `/cars/${car.id}` }]}>
            <Head title={`${car.nickname || `${car.make} ${car.model}`}`} />
            
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={cars.index.url()}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="h-4 w-4 mr-2" />
                                Back to Cars
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">
                                {car.nickname || `${car.make} ${car.model}`}
                            </h1>
                            <p className="text-muted-foreground">
                                {car.make} {car.model} ({car.year})
                            </p>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Link href={cars.modifications.create.url({ car })}>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Modification
                            </Button>
                        </Link>
                        <Link href={cars.edit.url(car)}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit Car
                            </Button>
                        </Link>
                        <Button 
                            variant="destructive" 
                            onClick={() => setShowDeleteConfirm(true)}
                        >
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Car Details */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Car Details</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <h4 className="font-medium text-sm text-muted-foreground">Make & Model</h4>
                                    <p className="text-lg">{car.make} {car.model}</p>
                                </div>
                                
                                <div>
                                    <h4 className="font-medium text-sm text-muted-foreground">Year</h4>
                                    <p className="text-lg">{car.year}</p>
                                </div>

                                {car.nickname && (
                                    <div>
                                        <h4 className="font-medium text-sm text-muted-foreground">Nickname</h4>
                                        <p className="text-lg">{car.nickname}</p>
                                    </div>
                                )}

                                {car.vin && (
                                    <div>
                                        <h4 className="font-medium text-sm text-muted-foreground">VIN</h4>
                                        <p className="font-mono text-sm">{car.vin}</p>
                                    </div>
                                )}

                                <div>
                                    <h4 className="font-medium text-sm text-muted-foreground">Added</h4>
                                    <p className="text-sm">{formatDate(car.created_at)}</p>
                                </div>

                                {car.notes && (
                                    <div>
                                        <h4 className="font-medium text-sm text-muted-foreground">Notes</h4>
                                        <p className="text-sm whitespace-pre-wrap">{car.notes}</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Modifications */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Modifications</CardTitle>
                                        <CardDescription>
                                            {car.modifications?.length || 0} modifications installed
                                        </CardDescription>
                                    </div>
                                    <Link href={cars.modifications.create.url({ car })}>
                                        <Button size="sm">
                                            <Plus className="mr-2 h-4 w-4" />
                                            Add Modification
                                        </Button>
                                    </Link>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {!car.modifications || car.modifications.length === 0 ? (
                                    <div className="text-center py-8">
                                        <Wrench className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                                        <h3 className="text-lg font-semibold mb-2">No modifications yet</h3>
                                        <p className="text-muted-foreground mb-4">
                                            Start building your car by adding your first modification
                                        </p>
                                        <Link href={cars.modifications.create.url({ car })}>
                                            <Button>
                                                <Plus className="mr-2 h-4 w-4" />
                                                Add First Modification
                                            </Button>
                                        </Link>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {car.modifications.map((modification) => (
                                            <div key={modification.id} className="border rounded-lg p-4">
                                                <div className="flex items-start justify-between">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2 mb-2">
                                                            <h4 className="font-semibold">{modification.name}</h4>
                                                            <Badge variant={modification.is_active ? "default" : "secondary"}>
                                                                {modification.is_active ? "Active" : "Inactive"}
                                                            </Badge>
                                                        </div>
                                                        <p className="text-sm text-muted-foreground mb-2">
                                                            {modification.category}
                                                            {modification.brand && ` • ${modification.brand}`}
                                                        </p>
                                                        {modification.notes && (
                                                            <p className="text-sm mb-2">{modification.notes}</p>
                                                        )}
                                                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                                            {modification.installation_date && (
                                                                <div className="flex items-center gap-1">
                                                                    <Calendar className="h-4 w-4" />
                                                                    {formatDate(modification.installation_date)}
                                                                </div>
                                                            )}
                                                            {modification.cost && (
                                                                <div className="flex items-center gap-1">
                                                                    <DollarSign className="h-4 w-4" />
                                                                    {formatCurrency(modification.cost)}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                    <div className="flex gap-2">
                                                        <Link href={cars.modifications.edit.url({ car, modification })}>
                                                            <Button variant="ghost" size="sm">
                                                                <Edit className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Delete Confirmation Dialog */}
                {showDeleteConfirm && (
                    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <Card className="w-full max-w-md mx-4">
                            <CardHeader>
                                <CardTitle>Delete Car</CardTitle>
                                <CardDescription>
                                    Are you sure you want to delete this car? This action cannot be undone.
                                    All modifications will also be deleted.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex gap-2 justify-end">
                                    <Button 
                                        variant="outline" 
                                        onClick={() => setShowDeleteConfirm(false)}
                                    >
                                        Cancel
                                    </Button>
                                    <Button variant="destructive" onClick={handleDelete}>
                                        Delete Car
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
