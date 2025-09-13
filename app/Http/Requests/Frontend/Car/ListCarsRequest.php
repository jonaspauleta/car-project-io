<?php

declare(strict_types=1);

namespace App\Http\Requests\Frontend\Car;

use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\Traits\Filterable;
use App\Http\Requests\Traits\Searchable;
use App\Http\Requests\Traits\Sortable;
use App\Repositories\CarRepository;

class ListCarsRequest extends PaginatedRequest
{
    use Filterable, Searchable, Sortable;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->allowsSearch(),
            ...$this->allowedSorts(CarRepository::ALLOWED_SORTS),
            ...$this->allowedFilters(CarRepository::ALLOWED_FILTERS),
        ];
    }
}
