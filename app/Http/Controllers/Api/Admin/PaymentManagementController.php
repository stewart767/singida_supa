<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentManagementController extends Controller
{
    public function __construct(
        protected PaymentVerificationService $paymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['application.applicant.user', 'application.programme']);

        if ($request->search) {
            $search = $request->search;
            $query->where('control_number', 'like', "%{$search}%")
                ->orWhereHas('application.applicant.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(PaymentResource::collection($payments)->response()->getData(true));
    }

    public function verify(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('verify', $payment);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:paid,rejected,pending,cancelled'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
        ]);

        if (!empty($validated['payment_method'])) {
            $payment->payment_method = $validated['payment_method'];
        }
        if (!empty($validated['transaction_reference'])) {
            $payment->transaction_reference = $validated['transaction_reference'];
        }
        $payment->save();

        $updatedPayment = $this->paymentService->verifyPayment(
            $payment,
            Auth::user(),
            $validated['status'],
            $validated['rejection_reason'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => $validated['status'] === 'paid'
                ? "Payment of TZS " . number_format($updatedPayment->amount) . " (Control #: {$updatedPayment->control_number}) successfully approved!"
                : "Payment status updated to " . ucfirst($validated['status']),
            'data' => new PaymentResource($updatedPayment),
        ]);
    }

    public function duplicates(): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $summary = $this->paymentService->getDuplicateControlNumbersSummary();

        return response()->json([
            'success' => true,
            'count' => $summary->count(),
            'data' => $summary,
        ]);
    }

    public function regenerateControlNumber(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('regenerate', $payment);

        $validated = $request->validate([
            'custom_control_number' => ['nullable', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:255'],
            'force' => ['nullable', 'boolean'],
        ]);

        try {
            $oldCn = $payment->control_number;
            $updatedPayment = $this->paymentService->regenerateControlNumber(
                $payment,
                Auth::user(),
                $validated['custom_control_number'] ?? null,
                $validated['reason'] ?? null,
                (bool) ($validated['force'] ?? false)
            );

            $applicantName = $updatedPayment->application?->applicant?->user?->name ?? 'Applicant';

            return response()->json([
                'success' => true,
                'message' => "Control number successfully updated from '{$oldCn}' to '{$updatedPayment->control_number}' for {$applicantName}.",
                'data' => new PaymentResource($updatedPayment),
                'old_control_number' => $oldCn,
                'new_control_number' => $updatedPayment->control_number,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
