<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Payment;
use App\Models\Programme;
use Illuminate\Support\Facades\DB;

class ReportExporterService
{
    public function getAnalyticsSummary(): array
    {
        return [
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'Under Review')->count(),
            'approved_applications' => Application::where('status', 'Approved')->count(),
            'rejected_applications' => Application::where('status', 'Rejected')->count(),
            'total_revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
            'applications_per_programme' => Programme::withCount('applications')->get(['id', 'code', 'name', 'applications_count']),
            'applications_per_region' => DB::table('applicants')
                ->select('region', DB::raw('count(*) as count'))
                ->whereNotNull('region')
                ->groupBy('region')
                ->get(),
            'admission_categories' => Application::select('admission_category', DB::raw('count(*) as count'))
                ->groupBy('admission_category')
                ->get(),
            'applications_with_login' => Application::where('is_public_submission', false)->count(),
            'applications_without_login' => Application::where('is_public_submission', true)->count(),
        ];
    }

    public function getFilteredReportQuery(string $type, array $filters = [])
    {
        if ($type === 'applications' || $type === 'admitted') {
            $query = Application::with(['applicant.user', 'programme']);

            if ($type === 'admitted') {
                $query->where('status', 'Approved');
            }

            // Apply Date Filters (Year, Month, Start Date, End Date)
            if (!empty($filters['year'])) {
                $query->whereYear('created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('created_at', $filters['month']);
            }
            if (!empty($filters['start_date'])) {
                $query->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $query->whereDate('created_at', '<=', $filters['end_date']);
            }

            // Apply Status Filter
            if (!empty($filters['status'])) {
                $status = $filters['status'];
                if ($status === 'pending') {
                    $query->whereIn('status', ['Pending Payment', 'Under Review', 'Submitted', 'Verified', 'Waitlist']);
                } elseif ($status === 'active' || $status === 'approved') {
                    $query->where('status', 'Approved');
                } else {
                    $query->where('status', $status);
                }
            }

            return $query;
        } elseif ($type === 'payments') {
            $query = Payment::with(['application.applicant.user', 'application.programme']);

            // Apply Date Filters (Year, Month, Start Date, End Date)
            if (!empty($filters['year'])) {
                $query->whereYear('created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('created_at', $filters['month']);
            }
            if (!empty($filters['start_date'])) {
                $query->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $query->whereDate('created_at', '<=', $filters['end_date']);
            }

            // Apply Status Filter
            if (!empty($filters['status'])) {
                $status = $filters['status'];
                if ($status === 'paid' || $status === 'active') {
                    $query->where('payment_status', 'paid');
                } elseif ($status === 'pending') {
                    $query->where('payment_status', 'pending');
                } elseif ($status === 'rejected') {
                    $query->where('payment_status', 'rejected');
                } else {
                    $query->where('payment_status', $status);
                }
            }

            return $query;
        }

        throw new \InvalidArgumentException("Invalid report type: {$type}");
    }

    public function generateCsvReport(string $type = 'applications', array $filters = []): string
    {
        $headers = [];
        $rows = [];

        if ($type === 'applications') {
            $headers = ['Application Number', 'Applicant Name', 'Email', 'Programme', 'Category', 'Status', 'Submitted Date'];
            $apps = $this->getFilteredReportQuery('applications', $filters)->get();
            foreach ($apps as $app) {
                $rows[] = [
                    $app->application_number,
                    $app->applicant->user->name ?? 'N/A',
                    $app->applicant->user->email ?? 'N/A',
                    $app->programme->code ?? 'N/A',
                    $app->admission_category,
                    $app->status,
                    $app->submitted_at ? $app->submitted_at->toDateTimeString() : 'Draft',
                ];
            }
        } elseif ($type === 'payments') {
            $headers = ['Control Number', 'Applicant Name', 'Amount', 'Currency', 'Status', 'Verified Date'];
            $payments = $this->getFilteredReportQuery('payments', $filters)->get();
            foreach ($payments as $p) {
                $rows[] = [
                    $p->control_number,
                    $p->application->applicant->user->name ?? 'N/A',
                    $p->amount,
                    $p->currency,
                    $p->payment_status,
                    $p->verified_at ? $p->verified_at->toDateTimeString() : 'Pending',
                ];
            }
        } elseif ($type === 'admitted') {
            $headers = ['Admission Number', 'Student Full Name', 'Gender', 'Programme Admitted', 'Verification Status', 'Admission Date'];
            $apps = $this->getFilteredReportQuery('admitted', $filters)->get();
            foreach ($apps as $app) {
                $rows[] = [
                    $app->admission_number ?? ('SUPA/ADM/' . date('Y') . '/' . str_pad($app->id, 4, '0', STR_PAD_LEFT)),
                    $app->applicant->user->name ?? 'N/A',
                    $app->applicant->gender ?? 'N/A',
                    $app->programme->name ?? 'N/A',
                    'VERIFIED',
                    $app->updated_at ? $app->updated_at->toDateTimeString() : 'N/A',
                ];
            }
        }

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
