<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('coordinate-inspections', fn (User $user): bool => $user->canCoordinateInspections());

        Gate::define('view-all-claims', fn (\App\Models\User $user): bool => $user->hasRole('CEO'));

        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole('CEO') || $user->email === 'admin@gmail.com' ? true : null;
        });
    }
}
