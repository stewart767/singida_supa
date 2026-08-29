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

        $request->validate([
            'status' => ['required', 'in:paid,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $verifiedPayment = $this->paymentService->verifyPayment(
            $payment,
            Auth::user(),
            $request->status,
            $request->rejection_reason
        );

        return response()->json([
            'message' => "Payment marked as {$request->status}.",
            'payment' => new PaymentResource($verifiedPayment),
        ]);
    }
}
