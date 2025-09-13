<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\PaginatedRequest;
use App\Models\Car;
use App\Models\Modification;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @extends BaseRepository<Modification>
 */
class ModificationRepository extends BaseRepository
{
    public const ALLOWED_INCLUDES = ['car'];

    public const ALLOWED_SORTS = ['id', 'name', 'category', 'cost', 'installation_date'];

    public const ALLOWED_FILTERS = ['name', 'category', 'brand', 'vendor', 'is_active'];

    /**
     * Get all modifications for a specific car owned by the authenticated user.
     *
     * @return Paginator<int, Modification>
     */
    public function list(
        PaginatedRequest $request,
        Car $car,
    ): Paginator {
        /** @var QueryBuilder<Modification> $query */
        $query = QueryBuilder::for(Modification::class)
            ->allowedSorts(self::ALLOWED_SORTS)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->with(self::ALLOWED_INCLUDES)
            ->where('car_id', $car->id)
            ->whereHas('car', function ($query) {
                $query->where('user_id', auth()->id());
            });

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('category', 'like', "%{$request->search}%")
                    ->orWhere('brand', 'like', "%{$request->search}%")
                    ->orWhere('vendor', 'like', "%{$request->search}%");
            });
        }

        return $this->paginate($request, $query);
    }

    /**
     * Find a modification by ID.
     */
    public function show(
        Car $car,
        Modification $modification,
    ): ?Modification {
        // Since authorization is handled in the controller, we can use the model directly
        // and just apply the query builder for includes and other features
        return QueryBuilder::for(Modification::class)
            ->with(self::ALLOWED_INCLUDES)
            ->find($modification->id);
    }

    /**
     * Create a new modification.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(
        array $data,
    ): Modification {
        $modification = Modification::create($data);

        activity()
            ->performedOn($modification)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Modification created');

        return $modification;
    }

    /**
     * Update a modification.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(
        Modification $modification,
        array $data,
    ): Modification {
        activity()
            ->performedOn($modification)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Modification updated');

        $modification->update($data);

        return $modification;
    }

    /**
     * Delete a modification.
     */
    public function delete(
        Modification $modification,
    ): bool {
        activity()
            ->performedOn($modification)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $modification->toArray()])
            ->log('Modification deleted');

        return $modification->delete();
    }
}
