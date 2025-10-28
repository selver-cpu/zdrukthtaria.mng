<?php

namespace App\Providers;

use App\Models\Projektet;
use App\Models\ProcesiProjektit;
use App\Policies\ProjektPolicy;
use App\Policies\ProcesiProjektitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Projektet::class => ProjektPolicy::class,
        ProcesiProjektit::class => ProcesiProjektitPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
