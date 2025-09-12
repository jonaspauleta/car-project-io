<?php

namespace App\Http\Resources;

use App\Models\Modification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ModificationResource
 *
 * @mixin Modification
 */
class ModificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'name' => $this->name,
            'category' => $this->category,
            'notes' => $this->notes,
            'brand' => $this->brand,
            'vendor' => $this->vendor,
            'installation_date' => $this->installation_date,
            'cost' => $this->cost,
            'is_active' => $this->is_active,
            'car' => $this->car,
        ];
    }
}