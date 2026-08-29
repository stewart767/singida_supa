<?php

namespace App\Repositories\Contracts;

use App\Models\ExternalApplicationRedirect;
use Illuminate\Database\Eloquent\Collection;

interface ExternalApplicationRedirectRepositoryInterface
{
    /**
     * Find an external redirect record by ID.
     *
     * @param int $id
     * @return ExternalApplicationRedirect|null
     */
    public function find(int $id): ?ExternalApplicationRedirect;

    /**
     * Find an external redirect record by its tracking token.
     *
     * @param string $token
     * @return ExternalApplicationRedirect|null
     */
    public function findByToken(string $token): ?ExternalApplicationRedirect;

    /**
     * Create a new external redirect tracking record.
     *
     * @param array $data
     * @return ExternalApplicationRedirect
     */
    public function create(array $data): ExternalApplicationRedirect;

    /**
     * Get all external redirects for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function allForUser(int $userId): Collection;
}
