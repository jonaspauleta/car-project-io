<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Car\CreateCarRequest;
use App\Http\Requests\API\Car\ListCarsRequest;
use App\Http\Requests\API\Car\ShowCarRequest;
use App\Http\Requests\API\Car\UpdateCarRequest;
use App\Http\Requests\API\Car\UploadCarImageRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function __construct(
        private CarRepository $carRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/cars",
     *     summary="List all cars",
     *     description="Retrieve a paginated list of cars with optional filtering, sorting, and including related data",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (id, make, year)",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"id", "make", "year"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[make]",
     *         in="query",
     *         description="Filter by car make",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[model]",
     *         in="query",
     *         description="Filter by car model",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[year]",
     *         in="query",
     *         description="Filter by car year",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[nickname]",
     *         in="query",
     *         description="Filter by car nickname",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[vin]",
     *         in="query",
     *         description="Filter by VIN",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include[]",
     *         in="query",
     *         description="Include related data (user, modifications). Can be used multiple times: include[]=user&include[]=modifications",
     *         required=false,
     *
     *         @OA\Schema(type="array", @OA\Items(type="string", enum={"user", "modifications"}))
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CarResource")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function index(
        ListCarsRequest $request
    ): JsonResource {
        $this->authorize('viewAny', Car::class);

        return CarResource::collection(
            $this->carRepository->list($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/cars",
     *     summary="Create a new car",
     *     description="Create a new car for the authenticated user",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"make", "model", "year"},
     *
     *             @OA\Property(property="make", type="string", description="Car manufacturer", example="Toyota"),
     *             @OA\Property(property="model", type="string", description="Car model", example="Camry"),
     *             @OA\Property(property="year", type="integer", description="Manufacturing year", example=2020),
     *             @OA\Property(property="nickname", type="string", description="Optional nickname for the car", example="My Daily Driver"),
     *             @OA\Property(property="vin", type="string", description="Vehicle Identification Number", example="1HGBH41JXMN109186"),
     *             @OA\Property(property="notes", type="string", description="Additional notes about the car", example="Great condition, low mileage")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Car created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/CarResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(
        CreateCarRequest $request,
    ): JsonResource {
        $this->authorize('create', Car::class);

        return CarResource::make(
            $this->carRepository->create(
                $request->validated() + ['user_id' => auth()->user()->id]
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/cars/{car}",
     *     summary="Get a specific car",
     *     description="Retrieve details of a specific car by ID",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include[]",
     *         in="query",
     *         description="Include related data (user, modifications). Can be used multiple times: include[]=user&include[]=modifications",
     *         required=false,
     *
     *         @OA\Schema(type="array", @OA\Items(type="string", enum={"user", "modifications"}))
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/CarResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Car] {id}")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function show(
        ShowCarRequest $request,
        Car $car,
    ): JsonResource {
        $this->authorize('view', $car);

        return CarResource::make(
            $this->carRepository->show(
                $car
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/cars/{car}",
     *     summary="Update a car",
     *     description="Update an existing car's details",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="make", type="string", description="Car manufacturer", example="Toyota"),
     *             @OA\Property(property="model", type="string", description="Car model", example="Camry"),
     *             @OA\Property(property="year", type="integer", description="Manufacturing year", example=2020),
     *             @OA\Property(property="nickname", type="string", description="Optional nickname for the car", example="My Daily Driver"),
     *             @OA\Property(property="vin", type="string", description="Vehicle Identification Number", example="1HGBH41JXMN109186"),
     *             @OA\Property(property="image_url", type="string", description="URL to car image", example="https://example.com/car.jpg"),
     *             @OA\Property(property="notes", type="string", description="Additional notes about the car", example="Great condition, low mileage")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Car updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/CarResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Car] {id}")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(
        UpdateCarRequest $request,
        Car $car,
    ): JsonResource {
        $this->authorize('update', $car);

        return CarResource::make(
            $this->carRepository->update(
                $car,
                $request->validated()
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/cars/{car}",
     *     summary="Delete a car",
     *     description="Delete a car from the system",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Car deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Car] {id}")
     *         )
     *     )
     * )
     */
    public function destroy(
        Car $car,
    ): Response {
        $this->authorize('delete', $car);

        $this->carRepository->delete(
            $car
        );

        return response()->noContent();
    }

    /**
     * Upload an image for the specified car.
     *
     * @OA\Post(
     *     path="/api/cars/{car}/upload-image",
     *     summary="Upload car image",
     *     description="Upload an image for a specific car",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"image"},
     *
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file (jpeg, png, jpg, gif, webp, max 10MB)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/CarResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Car] {id}")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function uploadImage(
        UploadCarImageRequest $request,
        Car $car,
    ): JsonResource {
        $this->authorize('update', $car);

        // Delete old image if exists
        if ($car->image_url) {
            $oldImagePath = $car->getRawOriginal('image_url');
            if ($oldImagePath) {
                Storage::disk('private')->delete($oldImagePath);
            }
        }

        // Store the new image in private storage
        $imagePath = $request->file('image')->store('cars', 'private');
        $imageUrl = route('api.cars.image', ['car' => $car->id]);

        // Update car with new image URL (store the actual path for serving)
        $car->update(['image_url' => $imagePath]);

        return CarResource::make($car->fresh());
    }

    /**
     * Serve the car's image with proper authorization.
     *
     * @OA\Get(
     *     path="/api/cars/{car}/image",
     *     summary="Get car image",
     *     description="Retrieve the image for a specific car. Only the car owner can access this image.",
     *     tags={"Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="car",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Image file",
     *
     *         @OA\MediaType(
     *             mediaType="image/*"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not the car owner",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Car not found or no image",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Image not found")
     *         )
     *     )
     * )
     */
    public function image(
        Car $car,
    ): Response|JsonResponse {
        $this->authorize('view', $car);

        // Check if car has an image
        $imagePath = $car->getRawOriginal('image_url');
        if (! $imagePath || ! Storage::disk('private')->exists($imagePath)) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Get the file and determine its MIME type
        $file = Storage::disk('private')->get($imagePath);
        $mimeType = Storage::disk('private')->mimeType($imagePath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600', // Cache for 1 hour
        ]);
    }
}
