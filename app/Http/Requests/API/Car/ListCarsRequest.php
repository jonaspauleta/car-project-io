<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Car;

use App\Http\Requests\API\PaginatedAPIRequest;
use App\Http\Requests\Traits\Filterable;
use App\Http\Requests\Traits\Includable;
use App\Http\Requests\Traits\Sortable;

class ListCarsRequest extends PaginatedAPIRequest
{
    use Filterable, Includable, Sortable;

    /**
     * The allowed sorts.
     *
     * @var list<string>
     */
    protected $allowedSorts = ['id', 'make', 'year'];

    /**
     * The allowed filters.
     *
     * @var list<string>
     */
    protected $allowedFilters = ['make', 'model', 'year', 'nickname', 'vin'];

    /**
     * The allowed includes.
     *
     * @var list<string>
     */
    protected $allowedIncludes = ['user', 'modifications'];

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
            ...$this->allowedSorts($this->allowedSorts),
            ...$this->allowedFilters($this->allowedFilters),
            ...$this->allowedIncludes($this->allowedIncludes),
        ];
    }
}
