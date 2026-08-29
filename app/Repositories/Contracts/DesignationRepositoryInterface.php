<?php

namespace App\Repositories\Contracts;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Collection;

interface DesignationRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Designation;
    public function create(array $data): Designation;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
