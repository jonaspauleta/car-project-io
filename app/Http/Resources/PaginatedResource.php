<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base class for paginated resource collections
 */
abstract class PaginatedResource extends ResourceCollection
{
    protected $childResourceClass;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'data' => $this->collection->map(function ($item) use ($request) {
                if ($this->childResourceClass) {
                    return (new $this->childResourceClass($item))->toArray($request);
                }
                return $item->toArray($request);
            }),
        ];

        if ($this->resource instanceof LengthAwarePaginator) {
            $data['pagination'] = [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
            ];
        }

        return $data;
    }
}
