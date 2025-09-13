import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import cars from '@/routes/cars';
import { Head, Form } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';
import { Link } from '@inertiajs/react';

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
        title: 'Add Car',
        href: '/cars/create',
    },
];

export default function CarCreate() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add Car" />
            
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href={cars.index.url()}>
                        <Button variant="ghost" size="sm">
                            <ArrowLeft className="h-4 w-4 mr-2" />
                            Back to Cars
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Add New Car</h1>
                        <p className="text-muted-foreground">
                            Add a new car to your collection
                        </p>
                    </div>
                </div>

                {/* Form */}
                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Car Information</CardTitle>
                            <CardDescription>
                                Enter the details for your new car
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form
                                action={cars.store.url()}
                                method="post"
                                className="space-y-6"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="make">Make *</Label>
                                                <Input
                                                    id="make"
                                                    name="make"
                                                    placeholder="e.g., Toyota"
                                                    required
                                                />
                                                <InputError message={errors.make} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="model">Model *</Label>
                                                <Input
                                                    id="model"
                                                    name="model"
                                                    placeholder="e.g., Camry"
                                                    required
                                                />
                                                <InputError message={errors.model} />
                                            </div>
                                        </div>

                                        <div className="grid gap-6 md:grid-cols-2">
                                            <div className="space-y-2">
                                                <Label htmlFor="year">Year *</Label>
                                                <Input
                                                    id="year"
                                                    name="year"
                                                    type="number"
                                                    min="1900"
                                                    max={new Date().getFullYear() + 1}
                                                    placeholder="e.g., 2020"
                                                    required
                                                />
                                                <InputError message={errors.year} />
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="nickname">Nickname</Label>
                                                <Input
                                                    id="nickname"
                                                    name="nickname"
                                                    placeholder="e.g., My Daily Driver"
                                                />
                                                <InputError message={errors.nickname} />
                                            </div>
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="vin">VIN (Vehicle Identification Number)</Label>
                                            <Input
                                                id="vin"
                                                name="vin"
                                                placeholder="17-character VIN"
                                                maxLength={17}
                                                minLength={17}
                                            />
                                            <InputError message={errors.vin} />
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="notes">Notes</Label>
                                            <Input
                                                id="notes"
                                                name="notes"
                                                placeholder="Any additional notes about this car..."
                                            />
                                            <InputError message={errors.notes} />
                                        </div>

                                        <div className="flex gap-4 pt-4">
                                            <Button type="submit" disabled={processing}>
                                                {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                                                Add Car
                                            </Button>
                                            <Link href={cars.index.url()}>
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
