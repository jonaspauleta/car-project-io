<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Modification\CreateModificationRequest;
use App\Http\Requests\API\Modification\ListModificationsRequest;
use App\Http\Requests\API\Modification\ShowModificationRequest;
use App\Http\Requests\API\Modification\UpdateModificationRequest;
use App\Http\Resources\ModificationResource;
use App\Models\Car;
use App\Models\Modification;
use App\Repositories\ModificationRepository;

class ModificationController extends Controller
{
    public function __construct(
        private ModificationRepository $modificationRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/cars/{car}/modifications",
     *     summary="List all modifications for a car",
     *     description="Retrieve a paginated list of modifications for a specific car with optional filtering, sorting, and including related data",
     *     tags={"Modifications"},
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
     *         description="Sort field (id, name, category, cost, installation_date)",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"id", "name", "category", "cost", "installation_date"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by modification name",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[category]",
     *         in="query",
     *         description="Filter by modification category",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[brand]",
     *         in="query",
     *         description="Filter by modification brand",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[vendor]",
     *         in="query",
     *         description="Filter by modification vendor",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[is_active]",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *
     *         @OA\Schema(type="boolean")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include[]",
     *         in="query",
     *         description="Include related data (car). Can be used multiple times: include[]=car",
     *         required=false,
     *
     *         @OA\Schema(type="array", @OA\Items(type="string", enum={"car"}))
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ModificationResource")),
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
        ListModificationsRequest $request,
        Car $car,
    ) {
        $this->authorize('viewAny', [Modification::class, $car]);

        return ModificationResource::collection(
            $this->modificationRepository->list($request, $car)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/cars/{car}/modifications",
     *     summary="Create a new modification for a car",
     *     description="Create a new modification for a specific car",
     *     tags={"Modifications"},
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
     *             required={"name", "category"},
     *
     *             @OA\Property(property="name", type="string", description="Modification name", example="Cold Air Intake"),
     *             @OA\Property(property="category", type="string", description="Modification category", example="Engine"),
     *             @OA\Property(property="notes", type="string", description="Additional notes about the modification", example="Increases airflow and performance"),
     *             @OA\Property(property="brand", type="string", description="Brand of the modification", example="K&N"),
     *             @OA\Property(property="vendor", type="string", description="Vendor where purchased", example="AutoZone"),
     *             @OA\Property(property="installation_date", type="string", format="date-time", description="Date when modification was installed", example="2023-06-15T10:30:00Z"),
     *             @OA\Property(property="cost", type="number", format="float", description="Cost of the modification", example=299.99),
     *             @OA\Property(property="is_active", type="boolean", description="Whether the modification is currently active", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Modification created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/ModificationResource")
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
        CreateModificationRequest $request,
        Car $car,
    ) {
        $this->authorize('create', [Modification::class, $car]);

        return ModificationResource::make(
            $this->modificationRepository->create(
                $request->validated() + ['car_id' => $car->id]
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/cars/{car}/modifications/{modification}",
     *     summary="Get a specific modification for a car",
     *     description="Retrieve details of a specific modification for a car by ID",
     *     tags={"Modifications"},
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
     *         name="modification",
     *         in="path",
     *         description="Modification ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include[]",
     *         in="query",
     *         description="Include related data (car). Can be used multiple times: include[]=car",
     *         required=false,
     *
     *         @OA\Schema(type="array", @OA\Items(type="string", enum={"car"}))
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/ModificationResource")
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
     *         description="Modification not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Modification] {id}")
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
        ShowModificationRequest $request,
        Car $car,
        Modification $modification,
    ) {
        $this->authorize('view', $modification);

        return ModificationResource::make(
            $this->modificationRepository->show(
                $request,
                $car,
                $modification
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/cars/{car}/modifications/{modification}",
     *     summary="Update a modification for a car",
     *     description="Update an existing modification's details for a specific car",
     *     tags={"Modifications"},
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
     *         name="modification",
     *         in="path",
     *         description="Modification ID",
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
     *             @OA\Property(property="name", type="string", description="Modification name", example="Cold Air Intake"),
     *             @OA\Property(property="category", type="string", description="Modification category", example="Engine"),
     *             @OA\Property(property="notes", type="string", description="Additional notes about the modification", example="Increases airflow and performance"),
     *             @OA\Property(property="brand", type="string", description="Brand of the modification", example="K&N"),
     *             @OA\Property(property="vendor", type="string", description="Vendor where purchased", example="AutoZone"),
     *             @OA\Property(property="installation_date", type="string", format="date-time", description="Date when modification was installed", example="2023-06-15T10:30:00Z"),
     *             @OA\Property(property="cost", type="number", format="float", description="Cost of the modification", example=299.99),
     *             @OA\Property(property="is_active", type="boolean", description="Whether the modification is currently active", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Modification updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/ModificationResource")
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
     *         description="Modification not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Modification] {id}")
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
        UpdateModificationRequest $request,
        Car $car,
        Modification $modification,
    ) {
        $this->authorize('update', $modification);

        return ModificationResource::make(
            $this->modificationRepository->update(
                $modification,
                $request->validated()
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/cars/{car}/modifications/{modification}",
     *     summary="Delete a modification for a car",
     *     description="Delete a modification from a specific car",
     *     tags={"Modifications"},
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
     *         name="modification",
     *         in="path",
     *         description="Modification ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Modification deleted successfully"
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
     *         description="Modification not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Modification] {id}")
     *         )
     *     )
     * )
     */
    public function destroy(
        Car $car,
        Modification $modification
    ) {
        $this->authorize('delete', $modification);

        $this->modificationRepository->delete(
            $modification
        );

        return response()->noContent();
    }
}
