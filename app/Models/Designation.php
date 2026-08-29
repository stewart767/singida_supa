<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_code',
        'head_of_designation_id',
        'description',
        'status',
    ];

    public function headOfDesignation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_designation_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'designation_id');
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'designation_id');
    }
}
