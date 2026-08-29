<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_profession',
        'years_experience',
        'skills',
        'linkedin_url',
        'portfolio_url',
        'cv_path',
        'preferred_job_categories',
        'preferred_locations',
        'expected_salary',
        'availability_date',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'preferred_job_categories' => 'array',
            'preferred_locations' => 'array',
            'availability_date' => 'date',
            'expected_salary' => 'integer',
            'years_experience' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
