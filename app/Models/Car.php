<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="CarResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", description="Car ID", example=1),
 *     @OA\Property(property="make", type="string", description="Car manufacturer", example="Toyota"),
 *     @OA\Property(property="model", type="string", description="Car model", example="Camry"),
 *     @OA\Property(property="year", type="integer", description="Manufacturing year", example=2020),
 *     @OA\Property(property="nickname", type="string", description="Optional nickname for the car", example="My Daily Driver", nullable=true),
 *     @OA\Property(property="vin", type="string", description="Vehicle Identification Number", example="1HGBH41JXMN109186", nullable=true),
 *     @OA\Property(property="image_url", type="string", description="URL to car image", example="https://example.com/car.jpg", nullable=true),
 *     @OA\Property(property="notes", type="string", description="Additional notes about the car", example="Great condition, low mileage", nullable=true),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource", nullable=true),
 *     @OA\Property(property="modifications", type="array", @OA\Items(ref="#/components/schemas/ModificationResource"), nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", description="User ID", example=1),
 *     @OA\Property(property="name", type="string", description="User name", example="John Doe"),
 *     @OA\Property(property="email", type="string", description="User email", example="john@example.com")
 * )
 *
 * @OA\Schema(
 *     schema="ModificationResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", description="Modification ID", example=1),
 *     @OA\Property(property="name", type="string", description="Modification name", example="Performance Exhaust"),
 *     @OA\Property(property="description", type="string", description="Modification description", example="High-performance exhaust system", nullable=true),
 *     @OA\Property(property="cost", type="number", format="float", description="Modification cost", example=599.99, nullable=true),
 *     @OA\Property(property="installed_at", type="string", format="date", description="Installation date", example="2023-06-15", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *
 *     @OA\Property(property="first", type="string", description="Link to first page", example="/api/cars?page=1"),
 *     @OA\Property(property="last", type="string", description="Link to last page", example="/api/cars?page=10"),
 *     @OA\Property(property="prev", type="string", description="Link to previous page", example="/api/cars?page=1", nullable=true),
 *     @OA\Property(property="next", type="string", description="Link to next page", example="/api/cars?page=3", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *
 *     @OA\Property(property="current_page", type="integer", description="Current page number", example=2),
 *     @OA\Property(property="from", type="integer", description="First item number on current page", example=11),
 *     @OA\Property(property="last_page", type="integer", description="Last page number", example=10),
 *     @OA\Property(property="path", type="string", description="Base path for pagination links", example="/api/cars"),
 *     @OA\Property(property="per_page", type="integer", description="Items per page", example=10),
 *     @OA\Property(property="to", type="integer", description="Last item number on current page", example=20),
 *     @OA\Property(property="total", type="integer", description="Total number of items", example=100)
 * )
 */
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
     * Get the full URL for the car's image.
     */
    public function getImageUrlAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // If it's a storage path, return the secure route URL
        return route('api.cars.image', ['car' => $this->id]);
    }

    /**
     * Delete the car's image from storage.
     */
    public function deleteImage(): bool
    {
        if (! $this->image_url) {
            return true;
        }

        $imagePath = $this->getRawOriginal('image_url');
        $deleted = Storage::disk('private')->delete($imagePath);

        if ($deleted) {
            $this->update(['image_url' => null]);
        }

        return $deleted;
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
