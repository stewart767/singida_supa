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

    public function generateCsvReport(string $type = 'applications'): string
    {
        $headers = [];
        $rows = [];

        if ($type === 'applications') {
            $headers = ['Application Number', 'Applicant Name', 'Email', 'Programme', 'Category', 'Status', 'Submitted Date'];
            $apps = Application::with(['applicant.user', 'programme'])->get();
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
            $payments = Payment::with(['application.applicant.user'])->get();
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
