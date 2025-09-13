<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Modification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and recent activity.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Get car statistics
        $totalCars = Car::where('user_id', $user->id)->count();
        $totalModifications = Modification::whereHas('car', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $activeModifications = Modification::whereHas('car', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('is_active', true)->count();

        $totalSpent = Modification::whereHas('car', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNotNull('cost')->sum('cost');

        // Get recent cars (last 5)
        $recentCars = Car::where('user_id', $user->id)
            ->withCount('modifications')
            ->latest()
            ->limit(3)
            ->get();

        // Get recent modifications (last 5)
        $recentModifications = Modification::whereHas('car', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('car')
            ->latest()
            ->limit(3)
            ->get();

        // Get cars with most modifications
        $carsWithMostModifications = Car::where('user_id', $user->id)
            ->withCount('modifications')
            ->orderBy('modifications_count', 'desc')
            ->limit(3)
            ->get();

        // Get modification categories breakdown
        $modificationCategories = Modification::whereHas('car', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('dashboard', [
            'stats' => [
                'totalCars' => $totalCars,
                'totalModifications' => $totalModifications,
                'activeModifications' => $activeModifications,
                'totalSpent' => $totalSpent,
            ],
            'recentCars' => $recentCars,
            'recentModifications' => $recentModifications,
            'carsWithMostModifications' => $carsWithMostModifications,
            'modificationCategories' => $modificationCategories,
        ]);
    }
}
