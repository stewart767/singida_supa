<?php

namespace App\Repositories\Eloquent;

use App\Models\Vacancy;
use App\Repositories\Contracts\VacancyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VacancyRepository implements VacancyRepositoryInterface
{
    public function all(): Collection
    {
        return Vacancy::with(['designation', 'position', 'category'])->latest()->get();
    }

    public function allPublished(): Collection
    {
        return Vacancy::with(['designation', 'position', 'category'])
            ->where('status', 'Published')
            ->where('application_deadline', '>=', now()->toDateString())
            ->latest()
            ->get();
    }

    public function find(int $id): ?Vacancy
    {
        return Vacancy::with(['designation', 'position', 'category'])->find($id);
    }

    public function findByVacancyNumber(string $number): ?Vacancy
    {
        return Vacancy::with(['designation', 'position', 'category'])
            ->where('vacancy_number', $number)
            ->first();
    }

    public function create(array $data): Vacancy
    {
        return Vacancy::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $vacancy = $this->find($id);
        return $vacancy ? $vacancy->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $vacancy = $this->find($id);
        return $vacancy ? $vacancy->delete() : false;
    }
}
