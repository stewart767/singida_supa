<?php

namespace App\Repositories\Contracts;

use App\Models\CareerProfile;

interface CareerProfileRepositoryInterface
{
    /**
     * Find a career profile by user ID.
     *
     * @param int $userId
     * @return CareerProfile|null
     */
    public function findForUser(int $userId): ?CareerProfile;

    /**
     * Create a new career profile.
     *
     * @param array $data
     * @return CareerProfile
     */
    public function create(array $data): CareerProfile;

    /**
     * Update an existing career profile.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a career profile.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
