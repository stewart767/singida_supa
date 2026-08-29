<?php

use App\Http\Controllers\Api\Admin\ApplicationManagementController;
use App\Http\Controllers\Api\Admin\PaymentManagementController;
use App\Http\Controllers\Api\Admin\ProgrammeController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Applicant\ApplicationWizardController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Integrations\SingidaPaymentCallbackController;
use App\Http\Controllers\Api\Public\PublicPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/public')->group(function () {
    Route::get('/programmes', [PublicPortalController::class, 'programmes']);
    Route::post('/track-application', [PublicPortalController::class, 'trackApplication']);
    Route::post('/resume-otp/send', [PublicPortalController::class, 'sendResumeOtp']);
    Route::post('/resume-otp/verify', [PublicPortalController::class, 'verifyResumeOtp']);
    Route::post('/resume-direct', [PublicPortalController::class, 'resumeDirect']);
    Route::post('/careers/track-application', [PublicPortalController::class, 'trackJobApplication']);
    Route::post('/careers/resume-direct', [PublicPortalController::class, 'resumeJobDirect']);
    Route::get('/news', [PublicPortalController::class, 'news']);
    Route::get('/events', [PublicPortalController::class, 'events']);
    Route::get('/faqs', [PublicPortalController::class, 'faqs']);
    Route::get('/downloads', [PublicPortalController::class, 'downloads']);
    Route::post('/contact', [PublicPortalController::class, 'submitContact']);
    Route::get('/admission-letter/{verificationCode}/download', [PublicPortalController::class, 'downloadAdmissionLetter'])
        ->name('api.admission-letter.download');
});

/*
|--------------------------------------------------------------------------
| Singida ↔ SUPA payment callback (NMB via Singida)
|--------------------------------------------------------------------------
*/
Route::post('/v1/integrations/singida/payment-callback', SingidaPaymentCallbackController::class)
    ->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'userProfile']);
    });
});


