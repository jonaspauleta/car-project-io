<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\API\PaginatedAPIRequest;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @template TModel of Model
 */
class BaseRepository
{
    /**
     * @param PaginatedAPIRequest $request
     * @param QueryBuilder|Builder<Model> $queryBuilder
     *
     * @return Paginator
     */
    public function paginate(
        PaginatedAPIRequest $request,
        QueryBuilder|Builder $queryBuilder,
    ): Paginator {
        return $queryBuilder
            ->paginate(perPage: $request->getLimit())
            ->appends(key: $request->query());
    }
}
