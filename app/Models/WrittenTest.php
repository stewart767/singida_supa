<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WrittenTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'test_name',
        'assigned_date',
        'questions_file_path',
        'script_file_path',
        'marks',
        'status',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
