<?php

use App\Http\Controllers\Api\Applicant\ApplicationWizardController;
use App\Http\Controllers\Api\Admin\ApplicationManagementController;
use App\Http\Controllers\Api\Admin\PaymentManagementController;
use App\Http\Controllers\Api\Admin\ProgrammeController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\ApplicantWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\PublicWebController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\RecruitmentWebController;
use App\Http\Controllers\Web\PublicRecruitmentWebController;
use App\Http\Controllers\Web\CareerProfileController;
use App\Http\Controllers\Web\ExternalApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public University Portal Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicWebController::class, 'home'])->name('home');
Route::get('/programmes', [PublicWebController::class, 'programmes'])->name('public.programmes');
Route::get('/admission-requirements', [PublicWebController::class, 'admissionRequirements'])->name('public.requirements');
Route::get('/track-application', [PublicWebController::class, 'trackApplication'])->name('public.track');
Route::get('/news', [PublicWebController::class, 'news'])->name('public.news');
Route::get('/news/{news:slug}', [PublicWebController::class, 'newsShow'])->name('public.news.show');
Route::get('/events', [PublicWebController::class, 'events'])->name('public.events');
Route::get('/faqs', [PublicWebController::class, 'faqs'])->name('public.faqs');
Route::get('/downloads', [PublicWebController::class, 'downloads'])->name('public.downloads');
Route::get('/student-guide', [PublicWebController::class, 'studentGuide'])->name('public.student-guide');
Route::get('/downloads/admission-steps-guide', [PublicWebController::class, 'admissionStepsGuide'])->name('public.admission-steps-guide');
Route::get('/downloads/payment-guideline', [PublicWebController::class, 'paymentGuideline'])->name('public.payment-guideline');
Route::get('/downloads/admission-requirements-excel', [PublicWebController::class, 'downloadAdmissionExcel'])->name('public.download.admission-excel');
Route::get('/contact', [PublicWebController::class, 'contact'])->name('public.contact');
Route::get('/privacy-policy', [PublicWebController::class, 'privacyPolicy'])->name('public.privacy');
Route::get('/terms-and-conditions', [PublicWebController::class, 'termsAndConditions'])->name('public.terms');

// Authentication Web Pages & Actions
Route::get('/login', [PublicWebController::class, 'login'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);

Route::get('/register', [PublicWebController::class, 'register'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/verify-otp', [WebAuthController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Profile Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileWebController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileWebController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Student Applicant Portal & Wizard Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/applicant/apply-wizard', [ApplicantWebController::class, 'wizard'])
    ->name('applicant.wizard')
    ->middleware(['consent.accepted']);

Route::middleware(['auth'])->prefix('applicant')->group(function () {
    // Standalone Consent notice routes (accessible to authenticated users who haven't consented yet)
    Route::get('/consent-notice', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'showNotice'])->name('applicant.consent.notice');
    Route::post('/consent-notice/accept', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'acceptNotice'])->name('applicant.consent.accept');
    Route::post('/consent-notice/decline', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'declineNotice'])->name('applicant.consent.decline');
    Route::get('/consent-notice/download/{type}', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'downloadDocumentFile'])->name('applicant.consent.download-file');

    // Consent-protected routes
    Route::middleware(['consent.accepted'])->group(function () {
        Route::get('/dashboard', [ApplicantWebController::class, 'dashboard'])->name('applicant.dashboard');

        // Wizard Action Endpoints
        Route::post('/initial-consent', [ApplicationWizardController::class, 'saveInitialConsent']);
        Route::post('/personal-info', [ApplicationWizardController::class, 'savePersonalInfo']);
        Route::post('/academic-profile', [ApplicationWizardController::class, 'saveAcademicProfile']);
        Route::post('/upload-document', [ApplicationWizardController::class, 'uploadDocument']);
        Route::post('/request-control-number', [ApplicationWizardController::class, 'requestControlNumber']);
        Route::post('/applications/{application}/request-control-number', [ApplicationWizardController::class, 'requestControlNumber']);
        Route::post('/submit-payment', [ApplicationWizardController::class, 'submitPaymentReceipt']);
        Route::get('/payment-status', [ApplicationWizardController::class, 'checkPaymentStatus']);
        Route::post('/submit-final', [ApplicationWizardController::class, 'submitFinal']);
        Route::post('/applications/{application}/submit-final', [ApplicationWizardController::class, 'submitFinal']);
        Route::post('/guest-credentials', [ApplicationWizardController::class, 'saveGuestCredentials']);
        Route::post('/claim-account', [ApplicationWizardController::class, 'claimGuestAccount']);

        // Applicant Privacy & Consent Records
        Route::get('/privacy-consent', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'show'])->name('applicant.privacy-consent');
        Route::get('/privacy-consent/receipt/{consent}', [\App\Http\Controllers\Web\ApplicantConsentController::class, 'downloadReceipt'])->name('applicant.privacy-consent.receipt');
    });
});

/*
|--------------------------------------------------------------------------
| Staff & Admin Management Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\AdminPermissionMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/applications', [AdminWebController::class, 'applications'])->name('admin.applications.index');
    Route::post('/applications', [AdminWebController::class, 'storeApplication'])->name('admin.applications.store');
    Route::get('/applications/{application}', [AdminWebController::class, 'showApplication'])->name('admin.applications.show');
    Route::get('/payments', [AdminWebController::class, 'payments'])->name('admin.payments.index');
    Route::get('/programmes', [AdminWebController::class, 'programmes'])->name('admin.programmes.index');
    Route::post('/programmes', [AdminWebController::class, 'storeProgramme'])->name('admin.programmes.store');
    Route::put('/programmes/{programme}', [AdminWebController::class, 'updateProgramme'])->name('admin.programmes.update');
    Route::delete('/programmes/{programme}', [AdminWebController::class, 'deleteProgramme'])->name('admin.programmes.destroy');
    Route::get('/cms', [AdminWebController::class, 'cms'])->name('admin.cms.index');
    Route::post('/cms/logos', [AdminWebController::class, 'updateLogos'])->name('admin.cms.logos');
    Route::post('/cms/sliders', [AdminWebController::class, 'updateSliders'])->name('admin.cms.sliders');
    Route::post('/cms/page-banners', [AdminWebController::class, 'updatePageBanners'])->name('admin.cms.page-banners');
    Route::post('/cms/page-banners/delete', [AdminWebController::class, 'deletePageBanner'])->name('admin.cms.page-banners.delete');
    Route::post('/cms/policies', [AdminWebController::class, 'updatePolicySettings'])->name('admin.cms.policies');
    Route::post('/cms/about', [AdminWebController::class, 'updateAbout'])->name('admin.cms.about');
    Route::post('/cms/footer', [AdminWebController::class, 'updateFooter'])->name('admin.cms.footer');
    Route::post('/cms/programmes/featured', [AdminWebController::class, 'toggleFeaturedProgramme'])->name('admin.cms.programmes.featured');
    Route::post('/cms/programmes/photo', [AdminWebController::class, 'updateProgrammePhoto'])->name('admin.cms.programmes.photo');
    Route::post('/cms/programmes/categories', [AdminWebController::class, 'updateProgrammeCategories'])->name('admin.cms.programmes.categories');
    Route::post('/cms/media', [AdminWebController::class, 'uploadMedia'])->name('admin.cms.media.store');
    Route::post('/cms/media/delete', [AdminWebController::class, 'deleteMedia'])->name('admin.cms.media.delete');
    Route::post('/cms/settings', [AdminWebController::class, 'updateSettings'])->name('admin.cms.settings');
    Route::post('/cms/storage-link', [AdminWebController::class, 'createStorageLink'])->name('admin.cms.storage-link');
    Route::post('/cms/fix-urls', [AdminWebController::class, 'fixDatabaseUrls'])->name('admin.cms.fix-urls');
    Route::post('/cms/users', [AdminWebController::class, 'storeUser'])->name('admin.cms.users.store');
    Route::put('/cms/users/{user}', [AdminWebController::class, 'updateUser'])->name('admin.cms.users.update');
    Route::post('/cms/users/{user}/status', [AdminWebController::class, 'toggleUserStatus'])->name('admin.cms.users.status');
    Route::delete('/cms/users/{user}', [AdminWebController::class, 'destroyUser'])->name('admin.cms.users.destroy');
    Route::post('/cms/roles', [AdminWebController::class, 'storeRole'])->name('admin.cms.roles.store');
    Route::post('/cms/roles/{role}/permissions', [AdminWebController::class, 'updateRolePermissions'])->name('admin.cms.roles.permissions');
    Route::delete('/cms/roles/{role}', [AdminWebController::class, 'destroyRole'])->name('admin.cms.roles.destroy');
    Route::post('/cms/audit-logs/clear', [AdminWebController::class, 'clearAuditLogs'])->name('admin.cms.audit_logs.clear');
    Route::post('/cms/comm/send', [AdminWebController::class, 'sendCommunicationAlert'])->name('admin.cms.comm.send');
    Route::get('/reports/pdf', [AdminWebController::class, 'exportPdfReport'])->name('admin.reports.pdf');

    // News Page Management
    Route::post('/cms/news', [AdminWebController::class, 'storeNews'])->name('admin.cms.news.store');
    Route::put('/cms/news/{news}', [AdminWebController::class, 'updateNews'])->name('admin.cms.news.update');
    Route::post('/cms/news/{news}/featured', [AdminWebController::class, 'toggleFeaturedNews'])->name('admin.cms.news.featured');
    Route::delete('/cms/news/{news}', [AdminWebController::class, 'destroyNews'])->name('admin.cms.news.destroy');

    // Contact Page & Messages Management
    Route::post('/cms/contact/settings', [AdminWebController::class, 'updateContactSettings'])->name('admin.cms.contact.settings');
    Route::post('/cms/contact/messages/{contact}/read', [AdminWebController::class, 'toggleReadContact'])->name('admin.cms.contact.read');
    Route::delete('/cms/contact/messages/{contact}', [AdminWebController::class, 'destroyContact'])->name('admin.cms.contact.destroy');

    // Personal Data Compliance Admin Routes
    Route::get('/compliance', [\App\Http\Controllers\Web\ComplianceController::class, 'index'])->name('admin.compliance.index');
    Route::post('/compliance/privacy', [\App\Http\Controllers\Web\ComplianceController::class, 'storePrivacy'])->name('admin.compliance.privacy.store');
    Route::post('/compliance/privacy/publish/{policy}', [\App\Http\Controllers\Web\ComplianceController::class, 'publishPrivacy'])->name('admin.compliance.privacy.publish');
    Route::post('/compliance/terms', [\App\Http\Controllers\Web\ComplianceController::class, 'storeTerms'])->name('admin.compliance.terms.store');
    Route::post('/compliance/terms/publish/{terms}', [\App\Http\Controllers\Web\ComplianceController::class, 'publishTerms'])->name('admin.compliance.terms.publish');
    Route::get('/compliance/logs/export', [\App\Http\Controllers\Web\ComplianceController::class, 'exportLogs'])->name('admin.compliance.logs.export');
    Route::get('/compliance/logs/pdf', [\App\Http\Controllers\Web\ComplianceController::class, 'exportPdfLogs'])->name('admin.compliance.logs.pdf');

    // Compliance Document Management & Revisions
    Route::get('/compliance/privacy/{policy}/edit', [\App\Http\Controllers\Web\ComplianceController::class, 'editPrivacy'])->name('admin.compliance.privacy.edit');
    Route::put('/compliance/privacy/{policy}', [\App\Http\Controllers\Web\ComplianceController::class, 'updatePrivacy'])->name('admin.compliance.privacy.update');
    Route::get('/compliance/privacy/{policy}/preview', [\App\Http\Controllers\Web\ComplianceController::class, 'previewPrivacy'])->name('admin.compliance.privacy.preview');
    Route::post('/compliance/privacy/rollback/{policy}', [\App\Http\Controllers\Web\ComplianceController::class, 'rollbackPrivacy'])->name('admin.compliance.privacy.rollback');
    Route::delete('/compliance/privacy/{policy}', [\App\Http\Controllers\Web\ComplianceController::class, 'destroyPrivacy'])->name('admin.compliance.privacy.destroy');

    Route::get('/compliance/terms/{terms}/edit', [\App\Http\Controllers\Web\ComplianceController::class, 'editTerms'])->name('admin.compliance.terms.edit');
    Route::put('/compliance/terms/{terms}', [\App\Http\Controllers\Web\ComplianceController::class, 'updateTerms'])->name('admin.compliance.terms.update');
    Route::get('/compliance/terms/{terms}/preview', [\App\Http\Controllers\Web\ComplianceController::class, 'previewTerms'])->name('admin.compliance.terms.preview');
    Route::post('/compliance/terms/rollback/{terms}', [\App\Http\Controllers\Web\ComplianceController::class, 'rollbackTerms'])->name('admin.compliance.terms.rollback');
    Route::delete('/compliance/terms/{terms}', [\App\Http\Controllers\Web\ComplianceController::class, 'destroyTerms'])->name('admin.compliance.terms.destroy');
});

/*
|--------------------------------------------------------------------------
| Dynamic Storage Serving Route (Fallback for Live Shared Hosting without symlinks)
|--------------------------------------------------------------------------
*/
$serveStorage = function (string $path) {
    $path = ltrim($path, '/');
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        $filePath = public_path('storage/' . $path);
    }

    if (!file_exists($filePath) || is_dir($filePath)) {
        abort(404);
    }

    $mimeType = @mime_content_type($filePath) ?: 'application/octet-stream';

    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
};

Route::get('/storage/{path}', $serveStorage)->where('path', '.*')->name('storage.serve');
Route::get('/public/storage/{path}', $serveStorage)->where('path', '.*');

Route::prefix('api/v1/applicant')->middleware(['auth'])->group(function () {
    Route::get('/application', [ApplicationWizardController::class, 'currentApplication']);
    Route::post('/personal-info', [ApplicationWizardController::class, 'savePersonalInfo']);
    Route::post('/academic-profile', [ApplicationWizardController::class, 'saveAcademicProfile']);
    Route::post('/applications/{application}/upload-document', [ApplicationWizardController::class, 'uploadDocument']);
    Route::post('/request-control-number', [ApplicationWizardController::class, 'requestControlNumber']);
    Route::post('/applications/{application}/request-control-number', [ApplicationWizardController::class, 'requestControlNumber']);
    Route::post('/submit-payment', [ApplicationWizardController::class, 'submitPaymentReceipt']);
    Route::post('/submit-final', [ApplicationWizardController::class, 'submitFinal']);
    Route::post('/applications/{application}/submit-final', [ApplicationWizardController::class, 'submitFinal']);
});

Route::prefix('api/v1/admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard-metrics', [ReportController::class, 'dashboardMetrics'])->name('api.dashboard-metrics');
    Route::get('/export-report', [ReportController::class, 'exportCsv'])->name('api.export-report');

    Route::get('/applications', [ApplicationManagementController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationManagementController::class, 'show']);
    Route::post('/documents/{document}/verify', [ApplicationManagementController::class, 'verifyDocument']);
    Route::post('/applications/{application}/decision', [ApplicationManagementController::class, 'makeDecision']);
    Route::post('/applications/{application}/sync-singida', [ApplicationManagementController::class, 'syncToSingida']);
    Route::post('/applications/bulk-approve', [ApplicationManagementController::class, 'bulkApprove']);

    Route::get('/payments', [PaymentManagementController::class, 'index']);
    Route::post('/payments/{payment}/verify', [PaymentManagementController::class, 'verify']);

    Route::apiResource('programmes', ProgrammeController::class);
});

/*
|--------------------------------------------------------------------------
| Recruitment & ATS Routes
|--------------------------------------------------------------------------
*/
// Public Career Portal Routes
Route::get('/careers', [PublicRecruitmentWebController::class, 'index'])->name('public.careers.index');
Route::get('/careers/track-application', [PublicRecruitmentWebController::class, 'trackApplicationPage'])->name('public.careers.track');
Route::get('/careers/vacancy/{vacancy_number}', [PublicRecruitmentWebController::class, 'show'])->name('public.careers.show');
Route::get('/careers/jd/{vacancy_number}', [PublicRecruitmentWebController::class, 'downloadJd'])->name('public.careers.jd');

// Ajira Integration / Simulation Routes (kept for compatibility, though bypassed)
Route::get('/careers/ajira/register', [App\Http\Controllers\Web\AjiraSimulationController::class, 'register'])->name('public.careers.ajira.register');
Route::post('/careers/ajira/callback', [App\Http\Controllers\Web\AjiraSimulationController::class, 'callback'])->name('public.careers.ajira.callback');

// Public Career Application Submit & Save-Step routes (open to guests, but checks consent if logged in)
Route::get('/careers/apply/{vacancy_number}', [PublicRecruitmentWebController::class, 'applyForm'])->name('public.careers.apply')->middleware(['consent.accepted']);
Route::post('/careers/apply', [PublicRecruitmentWebController::class, 'submitApplication'])->name('public.careers.submit')->middleware(['consent.accepted']);
Route::post('/careers/apply/save-step', [PublicRecruitmentWebController::class, 'saveWizardStep'])->name('public.careers.apply.save-step')->middleware(['consent.accepted']);

// Authenticated & Consent-verified Applicant Recruitment Routes
Route::middleware(['auth', 'consent.accepted'])->group(function () {
    Route::get('/careers/apply/preview/{id}', [PublicRecruitmentWebController::class, 'downloadPdfPreview'])->name('public.careers.apply.preview');
    Route::get('/careers/apply-again/{vacancy_number}', [PublicRecruitmentWebController::class, 'applyAgain'])->name('public.careers.apply-again');

    // External application redirect routes
    Route::get('/careers/vacancy/{vacancy_number}/confirm', [ExternalApplicationController::class, 'confirmForm'])->name('careers.vacancy.confirm');
    Route::get('/careers/vacancy/{vacancy_number}/redirect', [ExternalApplicationController::class, 'processRedirect'])->name('careers.vacancy.redirect')->middleware(['signed', 'throttle:10,1']);

    Route::get('/careers/dashboard', [PublicRecruitmentWebController::class, 'dashboard'])->name('public.careers.dashboard');
    Route::post('/careers/offer-letter/{offerLetter}/sign', [PublicRecruitmentWebController::class, 'signOfferLetter'])->name('public.careers.offer-letter.sign');

    // Career Profile Routes
    Route::get('/career/profile', [CareerProfileController::class, 'show'])->name('career.profile.show');
    Route::get('/career/profile/create', [CareerProfileController::class, 'create'])->name('career.profile.create');
    Route::post('/career/profile', [CareerProfileController::class, 'store'])->name('career.profile.store');
    Route::get('/career/profile/edit', [CareerProfileController::class, 'edit'])->name('career.profile.edit');
    Route::put('/career/profile', [CareerProfileController::class, 'update'])->name('career.profile.update');

    // CV Download signed route
    Route::get('/career/profile/cv/{profile}', [CareerProfileController::class, 'downloadCv'])->name('career.profile.download-cv')->middleware(['signed', 'throttle:5,1']);
});

// Admin Recruitment Routes
Route::middleware(['auth'])->prefix('admin/recruitment')->group(function () {
    Route::get('/dashboard', [RecruitmentWebController::class, 'dashboard'])->name('admin.recruitment.dashboard');

    Route::get('/categories', [RecruitmentWebController::class, 'categories'])->name('admin.recruitment.categories');
    Route::post('/categories', [RecruitmentWebController::class, 'storeCategory'])->name('admin.recruitment.categories.store');
    Route::put('/categories/{category}', [RecruitmentWebController::class, 'updateCategory'])->name('admin.recruitment.categories.update');
    Route::delete('/categories/{category}', [RecruitmentWebController::class, 'destroyCategory'])->name('admin.recruitment.categories.destroy');

    Route::get('/designations', [RecruitmentWebController::class, 'designations'])->name('admin.recruitment.designations');
    Route::post('/designations', [RecruitmentWebController::class, 'storeDesignation'])->name('admin.recruitment.designations.store');
    Route::put('/designations/{designation}', [RecruitmentWebController::class, 'updateDesignation'])->name('admin.recruitment.designations.update');

    Route::get('/campuses', [RecruitmentWebController::class, 'campuses'])->name('admin.recruitment.campuses');
    Route::post('/campuses', [RecruitmentWebController::class, 'storeCampus'])->name('admin.recruitment.campuses.store');
    Route::put('/campuses/{campus}', [RecruitmentWebController::class, 'updateCampus'])->name('admin.recruitment.campuses.update');
    Route::delete('/campuses/{campus}', [RecruitmentWebController::class, 'deleteCampus'])->name('admin.recruitment.campuses.delete');

    Route::get('/positions', [RecruitmentWebController::class, 'positions'])->name('admin.recruitment.positions');
    Route::post('/positions', [RecruitmentWebController::class, 'storePosition'])->name('admin.recruitment.positions.store');
    Route::put('/positions/{position}', [RecruitmentWebController::class, 'updatePosition'])->name('admin.recruitment.positions.update');

    Route::get('/vacancies', [RecruitmentWebController::class, 'vacancies'])->name('admin.recruitment.vacancies');
    Route::post('/vacancies', [RecruitmentWebController::class, 'storeVacancy'])->name('admin.recruitment.vacancies.store');
    Route::put('/vacancies/{vacancy}', [RecruitmentWebController::class, 'updateVacancy'])->name('admin.recruitment.vacancies.update');
    Route::delete('/vacancies/{vacancy}', [RecruitmentWebController::class, 'destroyVacancy'])->name('admin.recruitment.vacancies.destroy');
    Route::post('/vacancies/{vacancy}/status/{action}', [RecruitmentWebController::class, 'toggleVacancyStatus'])->name('admin.recruitment.vacancies.status');

    Route::get('/applications', [RecruitmentWebController::class, 'applications'])->name('admin.recruitment.applications.index');
    Route::post('/applications/bulk-action', [RecruitmentWebController::class, 'bulkActionApplications'])->name('admin.recruitment.applications.bulk');
    Route::get('/applications/export-csv', [RecruitmentWebController::class, 'exportApplicationsCsv'])->name('admin.recruitment.applications.export');
    Route::get('/applications/{application}', [RecruitmentWebController::class, 'showApplication'])->name('admin.recruitment.applications.show');
    Route::post('/applications/{application}/stage', [RecruitmentWebController::class, 'updateApplicationStage'])->name('admin.recruitment.applications.stage');
    Route::post('/applications/{application}/credentials', [RecruitmentWebController::class, 'manageCredentials'])->name('admin.recruitment.applications.credentials');

    Route::get('/ats', [RecruitmentWebController::class, 'ats'])->name('admin.recruitment.ats');

    Route::get('/interviews', [RecruitmentWebController::class, 'interviews'])->name('admin.recruitment.interviews');
    Route::post('/interviews/schedule', [RecruitmentWebController::class, 'scheduleInterview'])->name('admin.recruitment.interviews.schedule');
    Route::get('/scores', [RecruitmentWebController::class, 'scores'])->name('admin.recruitment.scores');
    Route::post('/scores', [RecruitmentWebController::class, 'submitScorecard'])->name('admin.recruitment.scores.store');

    Route::get('/written-tests', [RecruitmentWebController::class, 'writtenTests'])->name('admin.recruitment.written-tests');
    Route::post('/written-tests/assign', [RecruitmentWebController::class, 'assignWrittenTest'])->name('admin.recruitment.written-tests.assign');
    Route::post('/written-tests/{test}/marks', [RecruitmentWebController::class, 'recordTestMarks'])->name('admin.recruitment.written-tests.marks');

    Route::get('/evaluations', [RecruitmentWebController::class, 'evaluations'])->name('admin.recruitment.evaluations');
    Route::post('/evaluations/{application}/decision', [RecruitmentWebController::class, 'submitFinalDecision'])->name('admin.recruitment.evaluations.decision');

    Route::get('/offer-letters', [RecruitmentWebController::class, 'offerLetters'])->name('admin.recruitment.offer-letters');
    Route::post('/offer-letters/generate', [RecruitmentWebController::class, 'generateOfferLetter'])->name('admin.recruitment.offer-letters.generate');

    Route::get('/talent-pool', [RecruitmentWebController::class, 'talentPool'])->name('admin.recruitment.talent-pool');
    Route::get('/reports', [RecruitmentWebController::class, 'reports'])->name('admin.recruitment.reports');
    Route::get('/reports/export', [RecruitmentWebController::class, 'exportCsv'])->name('admin.recruitment.reports.export');

    Route::get('/settings', [RecruitmentWebController::class, 'settings'])->name('admin.recruitment.settings');
    Route::post('/settings', [RecruitmentWebController::class, 'updateSettings'])->name('admin.recruitment.settings.update');
});

