<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'make',
        'model',
        'year',
        'nickname',
        'vin',
        'image_url',
        'notes',
    ];

    /**
     * Get the user that owns the car.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the modifications for the car.
     *
     * @return HasMany<Modification, $this>
     */
    public function modifications(): HasMany
    {
        return $this->hasMany(Modification::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return CarFactory
     */
    protected static function newFactory()
    {
        return CarFactory::new();
    }
}
