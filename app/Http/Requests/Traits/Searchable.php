<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

/**
 * @property string $search
 */
trait Searchable
{
    /**
     * @return array<string, string[]>
     */
    private function allowsSearch(): array
    {
        return [
            'search' => ['string', 'max:255'],
        ];
    }
}
