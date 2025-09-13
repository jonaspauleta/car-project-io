<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Car\ListCarsRequest;
use App\Http\Requests\Car\StoreCarRequest;
use App\Http\Requests\Car\UpdateCarRequest;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use App\Http\Resources\CarResource;
use Inertia\Response;

class CarController extends Controller
{
    public function __construct(
        private CarRepository $carRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListCarsRequest $request): Response
    {
        $this->authorize('viewAny', Car::class);

        $cars = $this->carRepository->list($request);

        return Inertia::render('Cars/Index', [
            'cars' => $cars->toResourceCollection(),
            'filters' => $request->only(['search', 'make', 'model', 'year']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Car::class);

        return Inertia::render('Cars/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request): RedirectResponse
    {
        $this->authorize('create', Car::class);

        $car = $this->carRepository->create(
            $request->validated() + ['user_id' => auth()->user()->id]
        );

        return redirect()
            ->route('cars.show', $car)
            ->with('success', 'Car created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car): Response
    {
        $this->authorize('view', $car);

        $car->load(['modifications' => function ($query) {
            $query->orderBy('installation_date', 'desc');
        }]);

        return Inertia::render('Cars/Show', [
            'car' => $car,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car): Response
    {
        $this->authorize('update', $car);

        return Inertia::render('Cars/Edit', [
            'car' => $car,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $this->authorize('update', $car);

        $this->carRepository->update($car, $request->validated());

        return redirect()
            ->route('cars.show', $car)
            ->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car): RedirectResponse
    {
        $this->authorize('delete', $car);

        $this->carRepository->delete($car);

        return redirect()
            ->route('cars.index')
            ->with('success', 'Car deleted successfully.');
    }
}
