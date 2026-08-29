<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\VacancyRepositoryInterface;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;
use App\Services\ExternalApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Exception;

class ExternalApplicationController extends Controller
{
    /**
     * @var VacancyRepositoryInterface
     */
    protected VacancyRepositoryInterface $vacancyRepo;

    /**
     * @var CareerProfileRepositoryInterface
     */
    protected CareerProfileRepositoryInterface $profileRepo;

    /**
     * @var ExternalApplicationService
     */
    protected ExternalApplicationService $externalService;

    /**
     * Constructor for ExternalApplicationController.
     *
     * @param VacancyRepositoryInterface $vacancyRepo
     * @param CareerProfileRepositoryInterface $profileRepo
     * @param ExternalApplicationService $externalService
     */
    public function __construct(
        VacancyRepositoryInterface $vacancyRepo,
        CareerProfileRepositoryInterface $profileRepo,
        ExternalApplicationService $externalService
    ) {
        $this->vacancyRepo = $vacancyRepo;
        $this->profileRepo = $profileRepo;
        $this->externalService = $externalService;
    }

    /**
     * Show the redirect confirmation page.
     *
     * @param string $vacancy_number
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function confirmForm(string $vacancy_number)
    {
        $vacancy = $this->vacancyRepo->findByVacancyNumber($vacancy_number);
        if (!$vacancy) {
            abort(404, 'Vacancy not found.');
        }

        if (!$vacancy->isExternal()) {
            return redirect()->route('public.careers.apply', $vacancy_number);
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if profile is complete
        $profile = $this->profileRepo->findForUser($user->id);
        if (!$profile) {
            return redirect()->route('career.profile.create')
                ->with('warning', 'You must complete your career profile and upload a CV before applying.');
        }

        // Generate temporary signed URL for the redirection step (expires in 10 minutes)
        $redirectUrl = URL::temporarySignedRoute(
            'careers.vacancy.redirect',
            now()->addMinutes(10),
            ['vacancy_number' => $vacancy->vacancy_number]
        );

        return view('public.careers.confirm', compact('vacancy', 'redirectUrl'));
    }

    /**
     * Handle the secure redirection logic.
     *
     * @param Request $request
     * @param string $vacancy_number
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processRedirect(Request $request, string $vacancy_number)
    {
        // 1. Validate temporary signed URL signature
        if (!$request->hasValidSignature()) {
            abort(401, 'This redirect link has expired or is invalid. Please try initiating the application again.');
        }

        $vacancy = $this->vacancyRepo->findByVacancyNumber($vacancy_number);
        if (!$vacancy) {
            abort(404, 'Vacancy not found.');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // 2. Initiate the redirect tracking and construct external URL
            $url = $this->externalService->initiateRedirect($user, $vacancy);

            // 3. Perform redirect
            return redirect()->away($url);
        } catch (Exception $e) {
            return redirect()->route('public.careers.show', $vacancy_number)
                ->with('error', $e->getMessage());
        }
    }
}
