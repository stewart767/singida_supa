<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCareerProfileRequest;
use App\Models\CareerProfile;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class CareerProfileController extends Controller
{
    /**
     * @var CareerProfileRepositoryInterface
     */
    protected CareerProfileRepositoryInterface $profileRepo;

    /**
     * Constructor for CareerProfileController.
     *
     * @param CareerProfileRepositoryInterface $profileRepo
     */
    public function __construct(CareerProfileRepositoryInterface $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    /**
     * Display the career profile.
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $this->profileRepo->findForUser($user->id);

        if (!$profile) {
            return redirect()->route('career.profile.create');
        }

        $this->authorize('view', $profile);

        // Generate temporary CV download URL valid for 10 minutes
        $cvDownloadUrl = URL::temporarySignedRoute(
            'career.profile.download-cv',
            now()->addMinutes(10),
            ['profile' => $profile->id]
        );

        return view('public.career-profile.show', compact('profile', 'cvDownloadUrl'));
    }

    /**
     * Show the form for creating a new career profile.
     */
    public function create()
    {
        $user = Auth::user();
        $profile = $this->profileRepo->findForUser($user->id);

        if ($profile) {
            return redirect()->route('career.profile.show');
        }

        $categories = \App\Models\JobCategory::where('status', 'active')->get();

        return view('public.career-profile.create', compact('categories'));
    }

    /**
     * Store a newly created career profile in storage.
     */
    public function store(StoreCareerProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $this->profileRepo->findForUser($user->id);

        if ($profile) {
            return redirect()->route('career.profile.show');
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('cv_file')) {
            $data['cv_path'] = $request->file('cv_file')->store('private/cv');
        }

        unset($data['cv_file']);

        $newProfile = $this->profileRepo->create($data);

        // Audit the career profile creation
        AuditLogService::log(
            'career_profile_created',
            "User {$user->email} created their career profile.",
            [
                'user_id' => $user->id,
                'entity_type' => 'CareerProfile',
                'entity_id' => $newProfile->id,
                'new_values' => $data,
            ]
        );

        return redirect()->route('career.profile.show')->with('success', 'Career Profile created successfully.');
    }

    /**
     * Show the form for editing the career profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $this->profileRepo->findForUser($user->id);

        if (!$profile) {
            return redirect()->route('career.profile.create');
        }

        $this->authorize('update', $profile);

        $categories = \App\Models\JobCategory::where('status', 'active')->get();

        return view('public.career-profile.edit', compact('profile', 'categories'));
    }

    /**
     * Update the career profile.
     */
    public function update(StoreCareerProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $this->profileRepo->findForUser($user->id);

        if (!$profile) {
            return redirect()->route('career.profile.create');
        }

        $this->authorize('update', $profile);

        $data = $request->validated();
        $oldValues = $profile->toArray();

        if ($request->hasFile('cv_file')) {
            if ($profile->cv_path) {
                Storage::delete($profile->cv_path);
            }
            $data['cv_path'] = $request->file('cv_file')->store('private/cv');
        }

        unset($data['cv_file']);

        $this->profileRepo->update($profile->id, $data);

        // Audit the career profile update
        AuditLogService::log(
            'career_profile_updated',
            "User {$user->email} updated their career profile.",
            [
                'user_id' => $user->id,
                'entity_type' => 'CareerProfile',
                'entity_id' => $profile->id,
                'old_values' => $oldValues,
                'new_values' => $data,
            ]
        );

        return redirect()->route('career.profile.show')->with('success', 'Career Profile updated successfully.');
    }

    /**
     * Download CV using a temporary signed route.
     */
    public function downloadCv(Request $request, CareerProfile $profile)
    {
        // 1. Validate the signature of the temporary route
        if (!$request->hasValidSignature()) {
            abort(401, 'Download link is expired or invalid.');
        }

        // 2. Authorize via policy
        $this->authorize('downloadCv', $profile);

        // 3. Return private file download
        $filePath = storage_path('app/' . $profile->cv_path);
        if (!file_exists($filePath)) {
            abort(404, 'CV file not found.');
        }

        return response()->download($filePath);
    }
}
