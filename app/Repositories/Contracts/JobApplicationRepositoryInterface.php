<?php

namespace App\Repositories\Contracts;

use App\Models\JobApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobApplicationRepositoryInterface
{
    public function find(int $id): ?JobApplication;
    public function findByApplicationNumber(string $number): ?JobApplication;
    public function getFilteredApplications(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): JobApplication;
    public function update(int $id, array $data): bool;
}
