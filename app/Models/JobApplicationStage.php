<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicationStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'stage',
        'assigned_hr_officer_id',
        'comments',
        'attachments',
        'notification_history',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'notification_history' => 'array',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function assignedHrOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_hr_officer_id');
    }
}
