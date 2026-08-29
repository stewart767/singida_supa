<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewScorecard extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'interviewer_id',
        'communication',
        'technical_knowledge',
        'problem_solving',
        'leadership',
        'teamwork',
        'confidence',
        'professionalism',
        'comments',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getAverageScoreAttribute(): float
    {
        $scores = [
            $this->communication,
            $this->technical_knowledge,
            $this->problem_solving,
            $this->leadership,
            $this->teamwork,
            $this->confidence,
            $this->professionalism,
        ];
        return array_sum($scores) / count($scores);
    }
}
