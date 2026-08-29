<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'privacy_policy_id',
        'terms_conditions_id',
        'consent_version',
        'consent_language',
        'consent_source',
        'device_type',
        'browser_name',
        'operating_system',
        'application_status_at_consent',
        'consent_given',
        'parent_consent_given',
        'parent_name',
        'parent_signature',
        'parent_consented_at',
        'ip_address',
        'user_agent',
        'consented_at',
        'withdrawn_at',
        'withdrawal_reason',
        'consent_hash',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'parent_consent_given' => 'boolean',
        'consented_at' => 'datetime',
        'parent_consented_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function privacyPolicy(): BelongsTo
    {
        return $this->belongsTo(PrivacyPolicy::class, 'privacy_policy_id');
    }

    public function termsCondition(): BelongsTo
    {
        return $this->belongsTo(TermsCondition::class, 'terms_conditions_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
