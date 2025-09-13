<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Illuminate\Http\Resources\MissingValue;

class JsonResource extends BaseJsonResource
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        if (!($this->resource instanceof Model)) {
            return;
        }

        /** @var array<string, mixed> $attributes */
        $attributes = $this->resource->getAttributes();

        $this->attributes = collect(value: $attributes)
            ->mapWithKeys(callback: fn ($value, $key) => [$key => $value])
            ->toArray();
    }

    /**
     * @param string $attribute
     * @param string|null $type
     *
     * @return MissingValue|mixed
     */
    protected function whenSelected(string $attribute, ?string $type = null): mixed
    {
        if (!($this->resource instanceof Model)) {
            return null;
        }

        $resource = $this->resource;

        $casts = $resource->getCasts();

        if (array_key_exists(key: $attribute, array: $this->attributes)) {
            $value = $this->attributes[$attribute];

            $hasCast = array_key_exists(key: $attribute, array: $casts);

            if (!$type && !$hasCast) {
                return $value;
            }

            $castType = $type ?? $casts[$attribute];

            return match ($castType) {
                'boolean' => (bool) $value,
                'double', 'float' => (float) $value,
                'integer' => (int) $value,
                'object' => (object) $value,
                'string' => (string) $value,
                'json', 'array' => (array) json_decode(json: $value, associative: true),
                'serialized' => unserialize(data: $value),
                default => $value,
            };
        }

        return new MissingValue();
    }
}
