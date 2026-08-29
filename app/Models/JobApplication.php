<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'user_id',
        'vacancy_id',
        'status',
        'current_step',
        'full_name',
        'gender',
        'date_of_birth',
        'nida_number',
        'tin_number',
        'nssf_number',
        'phone',
        'whatsapp_number',
        'email',
        'region',
        'district',
        'physical_address',
        'worked_at_sttc',
        'sttc_experience',
        'experience_history',
        'education_history',
        'ict_description',
        'ict_skills',
        'professional_qualifications',
        'referees',
        'motivation_letter',
        'attachments',
        'certified_correct',
        'digital_signature',
        'declaration_date',
        'submitted_at',
        'assigned_hr_officer_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'declaration_date' => 'date',
            'submitted_at' => 'datetime',
            'worked_at_sttc' => 'boolean',
            'certified_correct' => 'boolean',
            'sttc_experience' => 'array',
            'experience_history' => 'array',
            'education_history' => 'array',
            'ict_skills' => 'array',
            'professional_qualifications' => 'array',
            'referees' => 'array',
            'attachments' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function assignedHrOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_hr_officer_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(JobApplicationStage::class)->orderBy('created_at', 'asc');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function writtenTests(): HasMany
    {
        return $this->hasMany(WrittenTest::class);
    }

    public function offerLetter(): HasOne
    {
        return $this->hasOne(OfferLetter::class);
    }
}
