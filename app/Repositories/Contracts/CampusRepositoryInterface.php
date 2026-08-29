<?php

namespace App\Repositories\Contracts;

use App\Models\Campus;
use Illuminate\Support\Collection;

interface CampusRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Campus;
    public function create(array $data): Campus;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
