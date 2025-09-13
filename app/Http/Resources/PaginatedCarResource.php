<?php

namespace App\Http\Resources;

use App\Http\Resources\CarResource;

/**
 * Class PaginatedCarResource
 */
class PaginatedCarResource extends PaginatedResource
{
    protected $childResourceClass = CarResource::class;
}