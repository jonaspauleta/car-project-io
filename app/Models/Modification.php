<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ModificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modification extends Model
{
    /** @use HasFactory<\Database\Factories\ModificationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'name',
        'category',
        'notes',
        'brand',
        'vendor',
        'installation_date',
        'cost',
        'is_active',
    ];

    /**
     * Get the car that owns the modification.
     *
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return ModificationFactory
     */
    protected static function newFactory()
    {
        return ModificationFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'installation_date' => 'datetime',
            'cost' => 'float',
            'is_active' => 'boolean',
        ];
    }
}
