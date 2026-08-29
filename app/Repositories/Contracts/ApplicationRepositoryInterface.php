<?php

namespace App\Repositories\Contracts;

use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationRepositoryInterface
{
    public function getFilteredApplications(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findByApplicationNumber(string $appNumber): ?Application;

    public function getApplicantActiveApplication(int $applicantId): ?Application;

    public function searchApplicants(?string $search = null, ?string $status = null): LengthAwarePaginator;
}
