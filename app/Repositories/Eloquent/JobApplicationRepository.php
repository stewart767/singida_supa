<?php

namespace App\Repositories\Eloquent;

use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobApplicationRepository implements JobApplicationRepositoryInterface
{
    public function find(int $id): ?JobApplication
    {
        return JobApplication::with(['vacancy.designation', 'vacancy.position', 'user', 'stages.assignedHrOfficer', 'interviews.scorecards', 'writtenTests', 'offerLetter'])->find($id);
    }

    public function findByApplicationNumber(string $number): ?JobApplication
    {
        return JobApplication::with(['vacancy.designation', 'vacancy.position', 'user', 'stages.assignedHrOfficer', 'interviews.scorecards', 'writtenTests', 'offerLetter'])
            ->where('application_number', $number)
            ->first();
    }

    public function getFilteredApplications(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = JobApplication::with(['vacancy.designation', 'vacancy.position', 'user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['vacancy_id'])) {
            $query->where('vacancy_id', $filters['vacancy_id']);
        }

        if (!empty($filters['position_id'])) {
            $query->whereHas('vacancy', function ($q) use ($filters) {
                $q->where('position_id', $filters['position_id']);
            });
        }

        if (!empty($filters['designation_id'])) {
            $query->whereHas('vacancy', function ($q) use ($filters) {
                $q->where('designation_id', $filters['designation_id']);
            });
        }

        if (!empty($filters['job_category_id'])) {
            $query->whereHas('vacancy', function ($q) use ($filters) {
                $q->where('job_category_id', $filters['job_category_id']);
            });
        }

        // Filters based on direct columns
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        if (!empty($filters['district'])) {
            $query->where('district', $filters['district']);
        }

        if (!empty($filters['nida_number'])) {
            $query->where('nida_number', $filters['nida_number']);
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): JobApplication
    {
        return JobApplication::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $application = JobApplication::find($id);
        return $application ? $application->update($data) : false;
    }
}
