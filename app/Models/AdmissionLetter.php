<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'admission_number',
        'verification_code',
        'pdf_path',
        'qr_code_hash',
        'reporting_date',
        'generated_by',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'reporting_date' => 'date',
            'generated_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
