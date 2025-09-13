<?php

namespace App\Http\Resources;

/**
 * Class PaginatedModificationResource
 */
class PaginatedModificationResource extends PaginatedResource
{
    protected $childResourceClass = ModificationResource::class;
}