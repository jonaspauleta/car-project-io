import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import cars from '@/routes/cars';
import { type BreadcrumbItem, type Car } from '@/types';
import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';

interface ModificationCreateProps {
    car: Car;
}

export default function ModificationCreate({ car }: ModificationCreateProps) {
    const breadcrumbs: BreadcrumbItem[] = [
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
            title: 'Add Modification',
            href: `/cars/${car.id}/modifications/create`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Add Modification - ${car.nickname || `${car.make} ${car.model}`}`} />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href={cars.modifications.index.url({ car })}>
                        <Button variant="ghost" size="sm">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Modifications
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Add Modification</h1>
                        <p className="text-muted-foreground">Add a new modification to {car.nickname || `${car.make} ${car.model}`}</p>
                    </div>
                </div>

                {/* Form */}
                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Modification Information</CardTitle>
                            <CardDescription>Enter the details for your new modification</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form action={cars.modifications.store.url(car)} method="post" className="space-y-6">
                                {({ processing, errors }) => (
                                    <>
                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="name">Name *</Label>
                                                <Input id="name" name="name" placeholder="e.g., Performance Exhaust" required />
                                                <InputError message={errors.name} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="category">Category *</Label>
                                                <Input id="category" name="category" placeholder="e.g., Exhaust System" required />
                                                <InputError message={errors.category} />
                                            </div>
                                        </div>

                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="brand">Brand</Label>
                                                <Input id="brand" name="brand" placeholder="e.g., Borla" />
                                                <InputError message={errors.brand} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="vendor">Vendor</Label>
                                                <Input id="vendor" name="vendor" placeholder="e.g., Amazon, Local Shop" />
                                                <InputError message={errors.vendor} />
                                            </div>
                                        </div>

                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="installation_date">Installation Date</Label>
                                                <Input
                                                    id="installation_date"
                                                    name="installation_date"
                                                    type="date"
                                                    max={new Date().toISOString().split('T')[0]}
                                                />
                                                <InputError message={errors.installation_date} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="cost">Cost</Label>
                                                <Input id="cost" name="cost" type="number" step="0.01" min="0" placeholder="0.00" />
                                                <InputError message={errors.cost} />
                                            </div>
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="notes">Notes</Label>
                                            <Input id="notes" name="notes" placeholder="Any additional notes about this modification..." />
                                            <InputError message={errors.notes} />
                                        </div>

                                        <div className="flex items-center space-x-2">
                                            <Checkbox id="is_active" name="is_active" defaultChecked />
                                            <Label htmlFor="is_active">Active modification</Label>
                                        </div>
                                        <InputError message={errors.is_active} />

                                        <div className="flex gap-4 pt-4">
                                            <Button type="submit" disabled={processing}>
                                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                                Add Modification
                                            </Button>
                                            <Link href={cars.modifications.index.url({ car })}>
                                                <Button type="button" variant="outline">
                                                    Cancel
                                                </Button>
                                            </Link>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
