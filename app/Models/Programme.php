<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'department',
        'faculty',
        'description',
        'entry_requirements',
        'duration_years',
        'annual_fee',
        'monthly_fee',
        'application_fee',
        'is_active',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'annual_fee' => 'float',
            'monthly_fee' => 'float',
            'application_fee' => 'float',
            'is_active' => 'boolean',
            'duration_years' => 'integer',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
