<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class PaginatedAPIRequest extends BaseAPIRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['integer'],
            'page' => ['integer'],
        ];
    }

    public function getPage(): int
    {
        return (int) $this->query(key: 'page', default: '1');
    }

    public function getLimit(): int
    {
        $defaultPaginationSize = config(key: 'pagination.pagination_size');
        $maxPaginationSize = config(key: 'pagination.max_pagination_size');

        $requestedPaginationSize = (int) $this->query(key: 'per_page');

        if (
            $requestedPaginationSize &&
            ($requestedPaginationSize >= 1 && $requestedPaginationSize <= $maxPaginationSize)
        ) {
            return $requestedPaginationSize;
        }

        return $defaultPaginationSize;
    }
}
