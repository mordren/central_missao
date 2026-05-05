<?php

namespace App\Providers;

use App\Models\ActivityPhoto;
use App\Policies\ActivityPhotoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(ActivityPhoto::class, ActivityPhotoPolicy::class);
    }
}
