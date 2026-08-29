<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'nida_number',
        'voter_id_number',
        'nida_card_number',
        'work_id_number',
        'whatsapp_number',
        'region',
        'district',
        'ward',
        'nationality',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_relation',
        'passport_photo_path',
        'initial_consent_given',
        'initial_consent_version',
        'initial_consent_at',
        'consent_status',
        'consented_at',
        'privacy_policy_version',
        'terms_version',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'initial_consent_given' => 'boolean',
            'initial_consent_at' => 'datetime',
            'consented_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function academicProfile(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(AcademicProfile::class, Application::class);
    }
}
