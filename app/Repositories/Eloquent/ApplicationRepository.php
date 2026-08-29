<?php

namespace App\Repositories\Eloquent;

use App\Models\Application;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    public function getFilteredApplications(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Application::with([
            'applicant.user',
            'programme',
            'academicYear',
            'intake',
            'payment',
            'documents',
            'academicProfile',
            'admissionLetter'
        ]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhereHas('applicant.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['programme_id'])) {
            $query->where('programme_id', $filters['programme_id']);
        }

        if (!empty($filters['admission_category'])) {
            $query->where('admission_category', $filters['admission_category']);
        }

        if (!empty($filters['gender'])) {
            $query->whereHas('applicant', function ($q) use ($filters) {
                $q->where('gender', $filters['gender']);
            });
        }

        if (!empty($filters['region'])) {
            $query->whereHas('applicant', function ($q) use ($filters) {
                $q->where('region', $filters['region']);
            });
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function findByApplicationNumber(string $appNumber): ?Application
    {
        return Application::with(['applicant.user', 'programme', 'documents', 'payment', 'academicProfile', 'admissionLetter'])
            ->where('application_number', $appNumber)
            ->first();
    }

    public function getApplicantActiveApplication(int $applicantId): ?Application
    {
        return Application::with(['programme', 'documents', 'payment', 'academicProfile', 'admissionLetter'])
            ->where('applicant_id', $applicantId)
            ->latest('id')
            ->first();
    }

    public function searchApplicants(?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return $this->getFilteredApplications([
            'search' => $search,
            'status' => $status
        ]);
    }
}
