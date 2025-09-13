<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Car;

use App\Http\Requests\API\PaginatedAPIRequest;
use App\Http\Requests\Traits\Filterable;
use App\Http\Requests\Traits\Includable;
use App\Http\Requests\Traits\Sortable;
use App\Repositories\CarRepository;

class ListCarsRequest extends PaginatedAPIRequest
{
    use Filterable, Includable, Sortable;

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
            ...$this->allowedSorts(CarRepository::ALLOWED_SORTS),
            ...$this->allowedFilters(CarRepository::ALLOWED_FILTERS),
            ...$this->allowedIncludes(CarRepository::ALLOWED_INCLUDES),
        ];
    }
}
