<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use App\Rules\KeysIn;

trait Filterable
{
    /**
     * @param  string[]  $filters
     * @return array<string, array<int, KeysIn|string>>
     */
    private function allowedFilters(array $filters): array
    {
        return [
            'filter' => ['array', new KeysIn($filters)],
            'filter.*' => ['string'],
        ];
    }
}
