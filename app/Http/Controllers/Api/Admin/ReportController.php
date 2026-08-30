<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportExporterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        protected ReportExporterService $reportExporter
    ) {}

    public function dashboardMetrics(): JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('view_dashboard'), 403);

        return response()->json([
            'metrics' => $this->reportExporter->getAnalyticsSummary(),
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        if (!auth()->user()->hasPermissionTo('download_reports')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type', 'applications');
        $filters = $request->only(['year', 'month', 'start_date', 'end_date', 'status']);
        $csvContent = $this->reportExporter->generateCsvReport($type, $filters);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '_report_' . date('Ymd_His') . '.csv"',
        ]);
    }
}
