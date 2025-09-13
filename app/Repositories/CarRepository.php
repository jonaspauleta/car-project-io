<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\PaginatedRequest;
use App\Models\Car;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class CarRepository extends BaseRepository
{
    public const ALLOWED_INCLUDES = ['user', 'modifications'];

    public const ALLOWED_SORTS = ['id', 'make', 'year'];

    public const ALLOWED_FILTERS = ['make', 'model', 'year', 'nickname', 'vin'];

    /**
     * Get all cars.
     *
     * @return Collection<int, Car>
     */
    public function list(
        PaginatedRequest $request,
    ): Paginator {
        $query = QueryBuilder::for(Car::class)
            ->with(self::ALLOWED_INCLUDES)
            ->where('user_id', auth()->id())
            ->defaultSort('id')
            ->allowedSorts(self::ALLOWED_SORTS)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->where($request->has('search') && $request->search ? function ($q) use ($request) {
                $q->where('make', 'like', "%{$request->search}%")
                    ->orWhere('model', 'like', "%{$request->search}%")
                    ->orWhere('nickname', 'like', "%{$request->search}%")
                    ->orWhere('vin', 'like', "%{$request->search}%");
            } : null);

        return $this->paginate($request, $query);
    }

    /**
     * Find a car by ID.
     */
    public function show(
        Car $car,
    ): ?Car {
        // Since authorization is handled in the controller, we can use the model directly
        // and just apply the query builder for includes and other features
        return QueryBuilder::for(Car::class)
            ->with(self::ALLOWED_INCLUDES)
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
