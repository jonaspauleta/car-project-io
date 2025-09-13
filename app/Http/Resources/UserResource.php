<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\JsonResource;

/**
 * Class UserResource
 *
 * @mixin User
 */
class UserResource extends JsonResource
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
            'name' => $this->whenSelected('name'),
            'email' => $this->whenSelected('email'),
            'cars' => CarResource::collection($this->whenSelected('cars')),
        ];
    }
}