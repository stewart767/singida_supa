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
        return response()->json([
            'metrics' => $this->reportExporter->getAnalyticsSummary(),
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        $type = $request->get('type', 'applications');
        $csvContent = $this->reportExporter->generateCsvReport($type);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '_report_' . date('Ymd_His') . '.csv"',
        ]);
    }
}
