<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Car;
use App\Models\Modification;
use App\Policies\CarPolicy;
use App\Policies\ModificationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Car::class => CarPolicy::class,
        Modification::class => ModificationPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        JsonResource::withoutWrapping();
    }
}
