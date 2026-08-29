<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'admission_type',
        'college_name',
        'diploma_programme_name',
        'diploma_registration_number',
        'diploma_graduation_year',
        'gpa',
        'csee_number',
        'csee_year',
        'csee_school',
        'acsee_number',
        'acsee_year',
        'acsee_school',
        'acsee_combination',
        'acsee_subject1',
        'acsee_subject2',
        'acsee_subject3',
        'acsee_grade1',
        'acsee_grade2',
        'acsee_grade3',
        'acsee_gs_grade',
        'acsee_points',
    ];

    protected function casts(): array
    {
        return [
            'gpa' => 'float',
            'acsee_points' => 'float',
            'diploma_graduation_year' => 'integer',
            'csee_year' => 'integer',
            'acsee_year' => 'integer',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
