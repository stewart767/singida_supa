<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;
use App\Repositories\Eloquent\CareerProfileRepository;
use App\Repositories\Contracts\ExternalApplicationRedirectRepositoryInterface;
use App\Repositories\Eloquent\ExternalApplicationRedirectRepository;
use App\Repositories\Contracts\JobApplicationIntentRepositoryInterface;
use App\Repositories\Eloquent\JobApplicationIntentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CareerProfileRepositoryInterface::class, CareerProfileRepository::class);
        $this->app->bind(ExternalApplicationRedirectRepositoryInterface::class, ExternalApplicationRedirectRepository::class);
        $this->app->bind(JobApplicationIntentRepositoryInterface::class, JobApplicationIntentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
