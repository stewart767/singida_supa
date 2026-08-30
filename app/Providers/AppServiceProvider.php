<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\JobApplication;
use App\Models\Campus;
use App\Policies\ApplicationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProgrammePolicy;
use App\Policies\RecruitmentPolicy;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Repositories\Eloquent\ApplicationRepository;
use App\Repositories\Contracts\JobCategoryRepositoryInterface;
use App\Repositories\Eloquent\JobCategoryRepository;
use App\Repositories\Contracts\DesignationRepositoryInterface;
use App\Repositories\Eloquent\DesignationRepository;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Repositories\Eloquent\PositionRepository;
use App\Repositories\Contracts\VacancyRepositoryInterface;
use App\Repositories\Eloquent\VacancyRepository;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Repositories\Eloquent\JobApplicationRepository;
use App\Repositories\Contracts\CampusRepositoryInterface;
use App\Repositories\Eloquent\CampusRepository;
use App\Models\CareerProfile;
use App\Policies\CareerProfilePolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApplicationRepositoryInterface::class, ApplicationRepository::class);
        $this->app->bind(JobCategoryRepositoryInterface::class, JobCategoryRepository::class);
        $this->app->bind(DesignationRepositoryInterface::class, DesignationRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(VacancyRepositoryInterface::class, VacancyRepository::class);
        $this->app->bind(JobApplicationRepositoryInterface::class, JobApplicationRepository::class);
        $this->app->bind(CampusRepositoryInterface::class, CampusRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dynamically set app.url based on current HTTP host and detect HTTPS context, preserving subdirectories
        if (isset($_SERVER['HTTP_HOST'])) {
            $isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                        (str_contains($_SERVER['HTTP_HOST'], 'supa.ac.tz'));
            $scheme = $isSecure ? 'https' : 'http';
            
            $baseUrl = '';
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $baseUrl = str_replace(['/public/index.php', '/public', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
            }
            $detectedUrl = rtrim($scheme . '://' . $_SERVER['HTTP_HOST'] . $baseUrl, '/');
            config([
                'app.url' => $detectedUrl,
                'filesystems.disks.public.url' => $detectedUrl . '/storage',
            ]);
            \Illuminate\Support\Facades\URL::forceRootUrl($detectedUrl);
        }

        // Force HTTPS URL scheme on live domain (supa.ac.tz) or in production mode
        if ($this->app->environment('production') || 
            (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'supa.ac.tz')) ||
            config('app.force_https', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Safeguard: Automatically remove public/hot in production to guarantee compiled assets are used
        if ($this->app->environment('production') && file_exists(public_path('hot'))) {
            @unlink(public_path('hot'));
        }

        Blade::component('layouts.public', 'public-layout');
        Blade::component('layouts.app', 'app-layout');

        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Programme::class, ProgrammePolicy::class);

        Gate::policy(JobCategory::class, RecruitmentPolicy::class);
        Gate::policy(Designation::class, RecruitmentPolicy::class);
        Gate::policy(Position::class, RecruitmentPolicy::class);
        Gate::policy(Vacancy::class, RecruitmentPolicy::class);
        Gate::policy(JobApplication::class, RecruitmentPolicy::class);
        Gate::policy(Campus::class, RecruitmentPolicy::class);
        Gate::policy(CareerProfile::class, CareerProfilePolicy::class);

        $publicStoragePath = public_path('storage');
        $symlinkWorks = true;

        // Check if public/storage exists and handle link status
        if (!file_exists($publicStoragePath) && !is_link($publicStoragePath)) {
            try {
                if (function_exists('symlink')) {
                    @symlink(storage_path('app/public'), $publicStoragePath);
                } else {
                    $symlinkWorks = false;
                }
            } catch (\Throwable $e) {
                $symlinkWorks = false;
            }
        } else {
            if (!is_link($publicStoragePath)) {
                // Physical folder exists, so symbolic link cannot be used directly
                $symlinkWorks = false;
            } else {
                // It is a link, check if it is broken
                $target = @readlink($publicStoragePath);
                if (!$target || !file_exists($target)) {
                    // Broken link: try to delete and recreate it
                    try {
                        if (PHP_OS_FAMILY === 'Windows') {
                            @exec('rmdir "' . $publicStoragePath . '"');
                        } else {
                            @unlink($publicStoragePath);
                        }
                        if (function_exists('symlink')) {
                            @symlink(storage_path('app/public'), $publicStoragePath);
                        } else {
                            $symlinkWorks = false;
                        }
                    } catch (\Throwable $e) {
                        $symlinkWorks = false;
                    }
                }
            }
        }

        // If symlinks aren't working, dynamically fallback to storing directly in public/storage
        if (!$symlinkWorks) {
            config(['filesystems.disks.public.root' => public_path('storage')]);
        }

        // Determine active public root path for directory preparation
        $publicRoot = $symlinkWorks ? storage_path('app/public') : public_path('storage');

        // Ensure storage directories exist for live uploads
        $directories = [
            $publicRoot . '/passports',
            $publicRoot . '/receipts',
            $publicRoot . '/documents',
            $publicRoot . '/branding',
            $publicRoot . '/sliders',
            $publicRoot . '/cms',
            $publicRoot . '/media',
            $publicRoot . '/signatures',
            $publicRoot . '/job_documents',
            storage_path('app/private/cv'),
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }
            @chmod($dir, 0777);
        }
    }
}

