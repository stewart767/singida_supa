<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'salary',
        'benefits',
        'reporting_date',
        'employment_terms',
        'pdf_path',
        'digital_signature_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reporting_date' => 'date',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
