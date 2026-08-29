<?php

namespace App\Repositories\Contracts;

use App\Models\JobApplicationIntent;

interface JobApplicationIntentRepositoryInterface
{
    /**
     * Find an intent record by ID.
     *
     * @param int $id
     * @return JobApplicationIntent|null
     */
    public function find(int $id): ?JobApplicationIntent;

    /**
     * Create a new application intent record.
     *
     * @param array $data
     * @return JobApplicationIntent
     */
    public function create(array $data): JobApplicationIntent;

    /**
     * Update an application intent record.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Find a user's intent for a specific vacancy.
     *
     * @param int $userId
     * @param int $vacancyId
     * @return JobApplicationIntent|null
     */
    public function findForUserVacancy(int $userId, int $vacancyId): ?JobApplicationIntent;
}
