<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

trait Sortable
{
    /**
     * @param  string[]  $sorts
     * @return array<string, array<int, In|string>>
     */
    private function allowedSorts(array $sorts): array
    {
        return [
            'sort' => ['string', Rule::in($this->parseSorts($sorts))],
        ];
    }

    /**
     * @param  string[]  $sorts
     * @return string[]
     */
    private function parseSorts(array $sorts): array
    {
        $parsedSorts = [];

        foreach ($sorts as $sort) {
            $parsedSorts[] = $sort;
            $parsedSorts[] = '-'.$sort;
        }

        return $parsedSorts;
    }
}
