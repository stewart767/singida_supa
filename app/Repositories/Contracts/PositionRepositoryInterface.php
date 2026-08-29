<?php

namespace App\Repositories\Contracts;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

interface PositionRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Position;
    public function create(array $data): Position;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
