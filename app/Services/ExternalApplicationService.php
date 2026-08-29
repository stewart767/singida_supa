<?php

namespace App\Services;

use App\Models\Vacancy;
use App\Models\User;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;
use App\Repositories\Contracts\ExternalApplicationRedirectRepositoryInterface;
use App\Repositories\Contracts\JobApplicationIntentRepositoryInterface;
use Illuminate\Support\Str;
use App\Services\AuditLogService;
use Exception;

class ExternalApplicationService
{
    /**
     * @var CareerProfileRepositoryInterface
     */
    protected CareerProfileRepositoryInterface $profileRepo;

    /**
     * @var ExternalApplicationRedirectRepositoryInterface
     */
    protected ExternalApplicationRedirectRepositoryInterface $redirectRepo;

    /**
     * @var JobApplicationIntentRepositoryInterface
     */
    protected JobApplicationIntentRepositoryInterface $intentRepo;

    /**
     * Constructor for ExternalApplicationService.
     *
     * @param CareerProfileRepositoryInterface $profileRepo
     * @param ExternalApplicationRedirectRepositoryInterface $redirectRepo
     * @param JobApplicationIntentRepositoryInterface $intentRepo
     */
    public function __construct(
        CareerProfileRepositoryInterface $profileRepo,
        ExternalApplicationRedirectRepositoryInterface $redirectRepo,
        JobApplicationIntentRepositoryInterface $intentRepo
    ) {
        $this->profileRepo = $profileRepo;
        $this->redirectRepo = $redirectRepo;
        $this->intentRepo = $intentRepo;
    }

    /**
     * Handle the external redirect process and return the tracking URL.
     *
     * @param User $user
     * @param Vacancy $vacancy
     * @return string
     * @throws Exception
     */
    public function initiateRedirect(User $user, Vacancy $vacancy): string
    {
        // 1. Validate that the vacancy is externally managed
        if (!$vacancy->isExternal()) {
            throw new Exception("This vacancy is internally managed.");
        }

        // 2. Ensure the user has a completed CareerProfile
        $profile = $this->profileRepo->findForUser($user->id);
        if (!$profile) {
            throw new Exception("You must complete your career profile before applying.");
        }

        // Audit the start of external vacancy application
        AuditLogService::log(
            'vacancy_external_application_started',
            "User {$user->email} started external application process for vacancy {$vacancy->vacancy_number}.",
            [
                'user_id' => $user->id,
                'entity_type' => 'Vacancy',
                'entity_id' => $vacancy->id,
                'new_values' => [
                    'vacancy_number' => $vacancy->vacancy_number,
                    'application_type' => $vacancy->application_type,
                ],
            ]
        );

        // 3. Create JobApplicationIntent before redirecting
        $intent = $this->intentRepo->create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'started',
            'source' => 'supa-careers',
        ]);

        AuditLogService::log(
            'job_application_intent_created',
            "Job application intent created for vacancy {$vacancy->vacancy_number} status started.",
            [
                'user_id' => $user->id,
                'entity_type' => 'JobApplicationIntent',
                'entity_id' => $intent->id,
                'new_values' => [
                    'status' => 'started',
                    'source' => 'supa-careers',
                ],
            ]
        );

        // 4. Generate secure SHA-256 tracking token using Str::uuid()
        $token = hash('sha256', Str::uuid());

        // 5. Store ExternalApplicationRedirect
        $provider = $vacancy->external_provider ?: 'ajiramarket';
        $redirect = $this->redirectRepo->create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'provider' => $provider,
            'tracking_token' => $token,
            'destination_url' => $vacancy->external_url,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'redirected_at' => now(),
        ]);

        // 6. Record the action using AuditLogService
        AuditLogService::log(
            'external_job_redirect',
            "Redirected user {$user->email} to Ajira Market for vacancy {$vacancy->vacancy_number}.",
            [
                'user_id' => $user->id,
                'entity_type' => 'ExternalApplicationRedirect',
                'entity_id' => $redirect->id,
                'new_values' => [
                    'provider' => $provider,
                    'tracking_token' => $token,
                    'destination_url' => $vacancy->external_url,
                ],
            ]
        );

        // Update intent status to redirected
        $this->intentRepo->update($intent->id, [
            'status' => 'redirected',
        ]);

        // 7. Return a fully constructed redirect URL
        return $this->buildRedirectUrl($vacancy->external_url, $token);
    }

    /**
     * Build the redirect URL with supa source and reference tracking query parameters.
     *
     * @param string $baseUrl
     * @param string $token
     * @return string
     */
    public function buildRedirectUrl(string $baseUrl, string $token): string
    {
        $parsed = parse_url($baseUrl);
        $queryParams = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
        }

        $queryParams['source'] = 'supa';
        $queryParams['ref'] = $token;

        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host = isset($parsed['host']) ? $parsed['host'] : '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $user = isset($parsed['user']) ? $parsed['user'] : '';
        $pass = isset($parsed['pass']) ? ':' . $parsed['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed['path']) ? $parsed['path'] : '';
        $query = '?' . http_build_query($queryParams);
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";
    }
}
