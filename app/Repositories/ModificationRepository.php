<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\API\Modification\ListModificationsRequest;
use App\Http\Requests\API\Modification\ShowModificationRequest;
use App\Models\Car;
use App\Models\Modification;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class ModificationRepository extends BaseRepository
{
    public const ALLOWED_INCLUDES = ['car'];

    public const ALLOWED_SORTS = ['id', 'name', 'category', 'cost', 'installation_date'];

    public const ALLOWED_FILTERS = ['name', 'category', 'brand', 'vendor', 'is_active'];

    /**
     * Get all modifications for a specific car owned by the authenticated user.
     *
     * @return Collection<int, Modification>
     */
    public function list(
        ListModificationsRequest $request,
        Car $car,
    ): Paginator {
        $query = QueryBuilder::for(Modification::class)
            ->where('car_id', $car->id)
            ->whereHas('car', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->defaultSort('id')
            ->allowedSorts(self::ALLOWED_SORTS)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->allowedIncludes(self::ALLOWED_INCLUDES);

        return $this->paginate($request, $query);
    }

    /**
     * Find a modification by ID.
     */
    public function show(
        ShowModificationRequest $request,
        Car $car,
        Modification $modification,
    ): ?Modification {
        return QueryBuilder::for(Modification::class)
            ->where('car_id', $car->id)
            ->whereHas('car', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->allowedIncludes(self::ALLOWED_INCLUDES)
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
