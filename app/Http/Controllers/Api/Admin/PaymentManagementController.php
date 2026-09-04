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
        return response()->json([
            'message' => 'Manual payment verification is disabled. Payments are automatically verified via the banking gateway.',
        ], 403);
    }
}
