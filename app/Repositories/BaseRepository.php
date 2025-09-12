<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\PaginationRequest;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

final class BaseRepository
{
    public function paginate(
        PaginationRequest $request,
        QueryBuilder|Builder $queryBuilder,
    ): Paginator {
        return $queryBuilder
            ->paginate(perPage: $request->getLimit())
            ->appends(key: $request->query());
    }
}
