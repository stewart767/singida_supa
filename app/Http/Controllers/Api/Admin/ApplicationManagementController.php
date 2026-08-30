<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MakeAdmissionDecisionRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Services\AdmissionDecisionService;
use App\Services\ApplicationWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\DocumentRejectedMail;
use Illuminate\Support\Facades\Mail;

class ApplicationManagementController extends Controller
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepo,
        protected AdmissionDecisionService $decisionService,
        protected ApplicationWorkflowService $workflowService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Application::class);

        $filters = $request->only([
            'search', 'status', 'programme_id', 'admission_category', 'gender', 'region', 'sort_by', 'sort_order'
        ]);

        $perPage = (int) $request->get('per_page', 15);
        $applications = $this->applicationRepo->getFilteredApplications($filters, $perPage);

        return response()->json(ApplicationResource::collection($applications)->response()->getData(true));
    }

    public function show(Application $application): JsonResponse
    {
        $this->authorize('view', $application);

        $application->load(['applicant.user', 'programme', 'academicYear', 'intake', 'academicProfile', 'documents', 'payment', 'admissionLetter']);

        return response()->json([
            'application' => new ApplicationResource($application),
        ]);
    }

    public function verifyDocument(Request $request, ApplicationDocument $document): JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('verify_documents'), 403);

        $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'rejection_comment' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'verification_status' => $request->status,
            'rejection_comment' => $request->rejection_comment,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditLogService::log('document_verified', "Document ID {$document->id} marked as {$request->status}");

        if ($request->status === 'rejected') {
            $application = $document->application;
            $applicant = $application?->applicant;
            $user = $applicant?->user;

            if ($user && $user->email) {
                try {
                    $docName = str_replace('_', ' ', $document->document_type);
                    $comment = $request->rejection_comment ?: 'No reason provided';
                    
                    Mail::to($user->email)->send(new DocumentRejectedMail($user->name, $docName, $comment));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send document rejection email: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => "Document has been {$request->status}.",
            'document' => $document,
        ]);
    }

    public function makeDecision(MakeAdmissionDecisionRequest $request, Application $application): JsonResponse
    {
        $this->authorize('decide', $application);

        $decidedApp = $this->decisionService->makeDecision(
            $application,
            Auth::user(),
            $request->decision,
            $request->reason
        );

        return response()->json([
            'message' => "Application status updated to {$decidedApp->status}.",
            'application' => new ApplicationResource($decidedApp->fresh()),
        ]);
    }

    public function bulkApprove(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('make_admission_decisions'), 403);

        $request->validate([
            'application_ids' => ['required', 'array'],
            'application_ids.*' => ['exists:applications,id'],
        ]);

        $count = $this->decisionService->bulkApprove($request->application_ids, Auth::user());

        return response()->json([
            'message' => "Successfully approved {$count} applications.",
        ]);
    }

    /**
     * Send / re-send a SUPA application to Singida to obtain an NMB control number.
     */
    public function syncToSingida(Request $request, Application $application): JsonResponse
    {
        $this->authorize('view', $application);

        try {
            $payment = $this->workflowService->ensurePaymentWithSingidaControlNumber(
                $application->fresh(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment']),
                force: (bool) $request->boolean('force', true)
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Failed to sync application to Singida.',
            ], 502);
        }

        $application = $application->fresh(['payment', 'applicant.user', 'programme']);

        return response()->json([
            'message' => 'Application synced to Singida. NMB control number: '.$payment->control_number,
            'control_number' => $payment->control_number,
            'singida_admission_id' => $application->singida_admission_id,
            'application' => new ApplicationResource($application),
        ]);
    }
}
