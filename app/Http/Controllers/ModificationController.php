<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Modification\ListModificationsRequest;
use App\Http\Requests\Modification\StoreModificationRequest;
use App\Http\Requests\Modification\UpdateModificationRequest;
use App\Models\Car;
use App\Models\Modification;
use App\Repositories\ModificationRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ModificationController extends Controller
{
    public function __construct(
        private ModificationRepository $modificationRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListModificationsRequest $request, Car $car): Response
    {
        $this->authorize('viewAny', [Modification::class, $car]);

        $modifications = $this->modificationRepository->list($request, $car);

        return Inertia::render('Modifications/Index', [
            'car' => $car,
            'modifications' => $modifications,
            'filters' => $request->only(['search', 'name', 'category', 'brand', 'vendor', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Car $car): Response
    {
        $this->authorize('create', [Modification::class, $car]);

        return Inertia::render('Modifications/Create', [
            'car' => $car,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModificationRequest $request, Car $car): RedirectResponse
    {
        $this->authorize('create', [Modification::class, $car]);

        $modification = $this->modificationRepository->create(
            $request->validated() + ['car_id' => $car->id]
        );

        return redirect()
            ->route('cars.modifications.show', [$car, $modification])
            ->with('success', 'Modification created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car, Modification $modification): Response
    {
        $this->authorize('view', $modification);

        $modification->load('car');

        return Inertia::render('Modifications/Show', [
            'car' => $car,
            'modification' => $modification,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car, Modification $modification): Response
    {
        $this->authorize('update', $modification);

        return Inertia::render('Modifications/Edit', [
            'car' => $car,
            'modification' => $modification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModificationRequest $request, Car $car, Modification $modification): RedirectResponse
    {
        $this->authorize('update', $modification);

        $this->modificationRepository->update($modification, $request->validated());

        return redirect()
            ->route('cars.modifications.show', [$car, $modification])
            ->with('success', 'Modification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car, Modification $modification): RedirectResponse
    {
        $this->authorize('delete', $modification);

        $this->modificationRepository->delete($modification);

        return redirect()
            ->route('cars.modifications.index', $car)
            ->with('success', 'Modification deleted successfully.');
    }
}
