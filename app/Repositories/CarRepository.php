<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\API\Car\ListCarsRequest;
use App\Http\Requests\API\Car\ShowCarRequest;
use App\Models\Car;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class CarRepository extends BaseRepository
{
    /**
     * Get all cars.
     *
     * @return Collection<int, Car>
     */
    public function list(
        ListCarsRequest $request,
    ): Paginator {
        $query = QueryBuilder::for(Car::class)
            ->defaultSort('id')
            ->allowedSorts($request->allowedSorts)
            ->allowedFilters($request->allowedFilters)
            ->allowedIncludes($request->allowedIncludes);

        return $this->paginate($request, $query);
    }

    /**
     * Find a car by ID.
     */
    public function show(
        ShowCarRequest $request,
        Car $car,
    ): ?Car {
        return QueryBuilder::for(Car::class)
            ->allowedIncludes($request->allowedIncludes)
            ->find($car->id);
    }

    /**
     * Create a new car.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(
        array $data,
    ): Car {
        $car = Car::create($data);

        activity()
            ->performedOn($car)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Car created');

        return $car;
    }

    /**
     * Update a car.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(
        Car $car,
        array $data,
    ): Car {
        activity()
            ->performedOn($car)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Car updated');

        $car->update($data);

        return $car;
    }

    /**
     * Delete a car.
     */
    public function delete(
        Car $car,
    ): bool {
        activity()
            ->performedOn($car)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $car->toArray()])
            ->log('Car deleted');

        return $car->delete();
    }
}
