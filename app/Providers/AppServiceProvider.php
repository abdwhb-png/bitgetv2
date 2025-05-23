<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // app()->usePublicPath(base_path('/../public_html'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);

        JsonResource::withoutWrapping();

        // Implicitly grant "root" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('root')) {
                return true;
            }
        });
    }
}