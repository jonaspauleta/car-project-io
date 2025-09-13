import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/input-error';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Car, type Modification } from '@/types';
import cars from '@/routes/cars';
import { Head, Form, Link } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';

interface ModificationEditProps {
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
    {
        title: 'Edit',
        href: `/cars/${car.id}/modifications/${modification.id}/edit`,
    },
];

export default function ModificationEdit({ car, modification }: ModificationEditProps) {
    return (
        <AppLayout breadcrumbs={getBreadcrumbs(car, modification)}>
            <Head title={`Edit ${modification.name} - ${car.nickname || `${car.make} ${car.model}`}`} />
            
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href={cars.modifications.show.url({ car, modification })}>
                        <Button variant="ghost" size="sm">
                            <ArrowLeft className="h-4 w-4 mr-2" />
                            Back to Modification
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Edit Modification</h1>
                        <p className="text-muted-foreground">
                            Update the details for {modification.name}
                        </p>
                    </div>
                </div>

                {/* Form */}
                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Modification Information</CardTitle>
                            <CardDescription>
                                Update the details for your modification
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form
                                action={cars.modifications.update.url({ car, modification })}
                                method="put"
                                className="space-y-6"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="name">Name *</Label>
                                                <Input
                                                    id="name"
                                                    name="name"
                                                    defaultValue={modification.name}
                                                    placeholder="e.g., Performance Exhaust"
                                                    required
                                                />
                                                <InputError message={errors.name} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="category">Category *</Label>
                                                <Input
                                                    id="category"
                                                    name="category"
                                                    defaultValue={modification.category}
                                                    placeholder="e.g., Exhaust System"
                                                    required
                                                />
                                                <InputError message={errors.category} />
                                            </div>
                                        </div>

                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="brand">Brand</Label>
                                                <Input
                                                    id="brand"
                                                    name="brand"
                                                    defaultValue={modification.brand || ''}
                                                    placeholder="e.g., Borla"
                                                />
                                                <InputError message={errors.brand} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="vendor">Vendor</Label>
                                                <Input
                                                    id="vendor"
                                                    name="vendor"
                                                    defaultValue={modification.vendor || ''}
                                                    placeholder="e.g., Amazon, Local Shop"
                                                />
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
                                                    defaultValue={modification.installation_date ? modification.installation_date.split('T')[0] : ''}
                                                    max={new Date().toISOString().split('T')[0]}
                                                />
                                                <InputError message={errors.installation_date} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="cost">Cost</Label>
                                                <Input
                                                    id="cost"
                                                    name="cost"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    defaultValue={modification.cost || ''}
                                                    placeholder="0.00"
                                                />
                                                <InputError message={errors.cost} />
                                            </div>
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="notes">Notes</Label>
                                            <Input
                                                id="notes"
                                                name="notes"
                                                defaultValue={modification.notes || ''}
                                                placeholder="Any additional notes about this modification..."
                                            />
                                            <InputError message={errors.notes} />
                                        </div>

                                        <div className="flex items-center space-x-2">
                                            <Checkbox 
                                                id="is_active" 
                                                name="is_active" 
                                                defaultChecked={modification.is_active}
                                            />
                                            <Label htmlFor="is_active">Active modification</Label>
                                        </div>
                                        <InputError message={errors.is_active} />

                                        <div className="flex gap-4 pt-4">
                                            <Button type="submit" disabled={processing}>
                                                {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                                                Update Modification
                                            </Button>
                                            <Link href={cars.modifications.show.url({ car, modification })}>
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
