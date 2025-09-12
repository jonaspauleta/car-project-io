<?php

namespace App\Http\Resources;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'nickname' => $this->nickname,
            'vin' => $this->vin,
            'image_url' => $this->image_url,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'modifications' => $this->modifications,
        ];
    }
}