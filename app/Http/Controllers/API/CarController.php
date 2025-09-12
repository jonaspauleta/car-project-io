<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\CarRepository;
use App\Http\Requests\API\Car\ListCarsRequest;
use App\Http\Resources\PaginatedCarResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\API\Car\ShowCarRequest;
use App\Http\Requests\API\Car\UpdateCarRequest;
use App\Http\Requests\API\Car\CreateCarRequest;

class CarController extends Controller
{
    public function __construct(
        private CarRepository $carRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(
        ListCarsRequest $request
    ): ResourceCollection
    {
        return PaginatedCarResource::collection(
            $this->carRepository->list($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCarRequest $request)
    {
        return CarResource::make(
            $this->carRepository->create(
                $request->validated()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowCarRequest $request, Car $car)
    {
        return CarResource::make(
            $this->carRepository->show(
                $request,
                $car
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car) 
    {
        return CarResource::make(
            $this->carRepository->update(
                $car,
                $request->validated()
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $this->carRepository->delete(
            $car
        );
        
        return response()->noContent();
    }
}
