<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\PaginatedRequest;
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
     * @param  QueryBuilder|Builder<Model>  $queryBuilder
     */
    public function paginate(
        PaginatedRequest $request,
        QueryBuilder|Builder $queryBuilder,
    ): Paginator {
        return $queryBuilder
            ->paginate(perPage: $request->getLimit())
            ->appends(key: $request->query());
    }
}
