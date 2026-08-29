<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_number',
        'job_title',
        'department_name',
        'designation_id',
        'position_id',
        'job_category_id',
        'campus_id',
        'number_of_positions',
        'employment_type',
        'contract_type',
        'location',
        'recommended_region',
        'salary_range',
        'application_deadline',
        'closing_date',
        'responsibilities',
        'qualifications',
        'required_experience',
        'required_skills',
        'benefits',
        'attachments',
        'featured_image',
        'status',
        'requirements',
        'application_type',
        'external_url',
        'external_provider',
    ];

    protected function casts(): array
    {
        return [
            'application_deadline' => 'date',
            'closing_date' => 'date',
            'attachments' => 'array',
            'requirements' => 'array',
            'application_type' => 'string',
        ];
    }

    public function isExternal(): bool
    {
        return $this->application_type === 'external';
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
