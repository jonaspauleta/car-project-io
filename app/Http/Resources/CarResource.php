<?php

namespace App\Http\Resources;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Resources\JsonResource;

/**
 * Class CarResource
 *
 * @mixin Car
 */
class CarResource extends JsonResource
{    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenSelected('id'), 
            'make' => $this->whenSelected('make'),
            'model' => $this->whenSelected('model'),
            'year' => $this->whenSelected('year'),
            'nickname' => $this->whenSelected('nickname'),
            'vin' => $this->whenSelected('vin'),
            'image_url' => $this->whenSelected('image_url'),
            'notes' => $this->whenSelected('notes'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'modifications' => ModificationResource::collection($this->whenLoaded('modifications')),
        ];
    }
}