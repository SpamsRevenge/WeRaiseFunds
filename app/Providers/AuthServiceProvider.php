<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'ContactPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
       Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

    }
}
