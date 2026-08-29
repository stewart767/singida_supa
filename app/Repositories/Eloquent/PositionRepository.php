<?php

namespace App\Repositories\Eloquent;

use App\Models\Position;
use App\Repositories\Contracts\PositionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository implements PositionRepositoryInterface
{
    public function all(): Collection
    {
        return Position::with(['designation', 'category', 'reportsTo'])->orderBy('name')->get();
    }

    public function find(int $id): ?Position
    {
        return Position::with(['designation', 'category', 'reportsTo'])->find($id);
    }

    public function create(array $data): Position
    {
        return Position::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $position = $this->find($id);
        return $position ? $position->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $position = $this->find($id);
        return $position ? $position->delete() : false;
    }
}
