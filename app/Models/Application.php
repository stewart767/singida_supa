<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number',
        'applicant_id',
        'programme_id',
        'academic_year_id',
        'intake_id',
        'admission_type',
        'admission_category',
        'status',
        'rejection_reason',
        'digital_signature_path',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'is_public_submission',
        'singida_admission_id',
        'singida_synced_at',
        'current_step',
        'completion_percentage',
        'expires_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'singida_synced_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }

    public function academicProfile(): HasOne
    {
        return $this->hasOne(AcademicProfile::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function admissionLetter(): HasOne
    {
        return $this->hasOne(AdmissionLetter::class);
    }

    public function consent(): HasOne
    {
        return $this->hasOne(ApplicationConsent::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ApplicationActivity::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public static function getDocumentTypeLabels(): array
    {
        return [
            'csee_certificate' => 'Cheti cha Kidato cha IV (CSEE)',
            'acsee_certificate' => 'Cheti cha Kidato cha VI (ACSEE)',
            'diploma_certificate' => 'Cheti cha Stashahada (Diploma)',
            'transcript' => 'Matokeo / Transcript ya Masomo',
            'nida_id' => 'Kitambulisho (NIDA / Kura / Kazi)',
            'passport' => 'Picha ya Pasipoti (Passport Photo)',
        ];
    }

    public function getRequiredDocumentTypes(): array
    {
        if ($this->admission_type === 'Form Six') {
            return ['acsee_certificate', 'transcript'];
        }

        return ['csee_certificate', 'diploma_certificate', 'transcript'];
    }

    public function getMissingDocumentTypes(): array
    {
        $required = $this->getRequiredDocumentTypes();
        $uploaded = $this->documents()->pluck('document_type')->toArray();

        return array_values(array_diff($required, $uploaded));
    }

    public function hasAllRequiredDocuments(): bool
    {
        return empty($this->getMissingDocumentTypes());
    }

    public function getDocumentStatusBreakdown(): array
    {
        $requiredTypes = $this->getRequiredDocumentTypes();
        $uploadedDocs = $this->documents->keyBy('document_type');
        $labels = self::getDocumentTypeLabels();

        $breakdown = [];
        foreach ($requiredTypes as $type) {
            $doc = $uploadedDocs->get($type);
            $breakdown[] = [
                'type' => $type,
                'label' => $labels[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                'is_required' => true,
                'uploaded' => $doc !== null,
                'original_filename' => $doc?->original_filename,
                'verification_status' => $doc?->verification_status ?? 'pending',
                'rejection_comment' => $doc?->rejection_comment,
            ];
        }

        return $breakdown;
    }
}
