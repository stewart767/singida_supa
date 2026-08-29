<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'type',
        'date',
        'time',
        'venue',
        'meeting_link',
        'instructions',
        'panel_members',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'panel_members' => 'array',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function scorecards(): HasMany
    {
        return $this->hasMany(InterviewScorecard::class);
    }
}
