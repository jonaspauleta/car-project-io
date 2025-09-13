<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Car\ListCarsRequest;
use App\Http\Requests\Frontend\Car\StoreCarRequest;
use App\Http\Requests\Frontend\Car\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CarController extends Controller
{
    public function __construct(
        private CarRepository $carRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListCarsRequest $request): InertiaResponse
    {
        $this->authorize('viewAny', Car::class);

        $cars = CarResource::collection($this->carRepository->list($request));

        return Inertia::render('Cars/Index', [
            'cars' => $cars,
            'filters' => $request->only(['search', 'make', 'model', 'year']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
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

        $validated = $request->validated();

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cars', 'private');
            $validated['image_url'] = $imagePath;
        }

        // Remove the image file from validated data as we handle it separately
        unset($validated['image']);

        $car = $this->carRepository->create(
            $validated + ['user_id' => auth()->user()->id]
        );

        return redirect()
            ->route('cars.show', $car)
            ->with('success', 'Car created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car): InertiaResponse
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
    public function edit(Car $car): InertiaResponse
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

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $oldImagePath = $car->getRawOriginal('image_url');
            if ($oldImagePath && ! str_starts_with($oldImagePath, 'http')) {
                Storage::disk('private')->delete($oldImagePath);
            }

            $imagePath = $request->file('image')->store('cars', 'private');
            $validated['image_url'] = $imagePath;
        }

        unset($validated['image']);

        $this->carRepository->update($car, $validated);

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

    /**
     * Serve the car's image with proper authorization.
     */
    public function image(Car $car): Response|JsonResponse
    {
        $this->authorize('view', $car);

        // Check if car has an image
        $imagePath = $car->getRawOriginal('image_url');
        if (! $imagePath || ! Storage::disk('private')->exists($imagePath)) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Get the file and determine its MIME type
        $file = Storage::disk('private')->get($imagePath);
        $mimeType = Storage::disk('private')->mimeType($imagePath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600', // Cache for 1 hour
            'ETag' => md5($file), // Add ETag for better cache validation
        ]);
    }
}
