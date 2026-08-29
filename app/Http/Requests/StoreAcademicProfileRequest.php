<?php

namespace App\Http\Requests;

use App\Rules\CseeNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'admission_type' => ['required', 'in:Diploma,Form Six'],
            'programme_id' => ['required', 'exists:programmes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'intake_id' => ['required', 'exists:intakes,id'],

            // Diploma rules
            'college_name' => ['required_if:admission_type,Diploma', 'nullable', 'string', 'max:255'],
            'diploma_programme_name' => ['required_if:admission_type,Diploma', 'nullable', 'string', 'max:255'],
            'diploma_registration_number' => ['required_if:admission_type,Diploma', 'nullable', 'string', 'max:100'],
            'diploma_graduation_year' => ['required_if:admission_type,Diploma', 'nullable', 'integer', 'min:1990', 'max:' . date('Y')],
            'gpa' => ['required_if:admission_type,Diploma', 'nullable', 'numeric', 'min:0.0', 'max:5.0'],

            // Form Six rules
            'csee_number' => ['required_if:admission_type,Form Six', 'nullable', new CseeNumberRule],
            'csee_year' => ['required_if:admission_type,Form Six', 'nullable', 'integer', 'min:1990', 'max:' . date('Y')],
            'csee_school' => ['required_if:admission_type,Form Six', 'nullable', 'string', 'max:255'],
            'acsee_number' => ['required_if:admission_type,Form Six', 'nullable', new CseeNumberRule],
            'acsee_year' => ['required_if:admission_type,Form Six', 'nullable', 'integer', 'min:1990', 'max:' . date('Y')],
            'acsee_school' => ['required_if:admission_type,Form Six', 'nullable', 'string', 'max:255'],
            'acsee_combination' => ['required_if:admission_type,Form Six', 'nullable', 'string', 'max:50'],
            'acsee_subject1' => ['nullable', 'string', 'max:100'],
            'acsee_subject2' => ['nullable', 'string', 'max:100'],
            'acsee_subject3' => ['nullable', 'string', 'max:100'],
            'acsee_grade1' => ['nullable', 'string', 'in:A,B,C,D,E,S,F,a,b,c,d,e,s,f'],
            'acsee_grade2' => ['nullable', 'string', 'in:A,B,C,D,E,S,F,a,b,c,d,e,s,f'],
            'acsee_grade3' => ['nullable', 'string', 'in:A,B,C,D,E,S,F,a,b,c,d,e,s,f'],
            'acsee_gs_grade' => ['nullable', 'string', 'in:A,B,C,D,E,S,F,a,b,c,d,e,s,f'],
            'acsee_points' => ['required_if:admission_type,Form Six', 'nullable', 'numeric', 'min:1', 'max:30'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->input('admission_type') === 'Form Six') {
            $grade1 = $this->input('acsee_grade1');
            $grade2 = $this->input('acsee_grade2');
            $grade3 = $this->input('acsee_grade3');
            $gsGrade = $this->input('acsee_gs_grade');

            // Normalize grades to uppercase
            $this->merge([
                'acsee_grade1' => $grade1 ? strtoupper($grade1) : null,
                'acsee_grade2' => $grade2 ? strtoupper($grade2) : null,
                'acsee_grade3' => $grade3 ? strtoupper($grade3) : null,
                'acsee_gs_grade' => $gsGrade ? strtoupper($gsGrade) : null,
            ]);

            $grade1 = $this->input('acsee_grade1');
            $grade2 = $this->input('acsee_grade2');
            $grade3 = $this->input('acsee_grade3');

            // Auto-populate subjects from combination if not explicitly passed
            $combination = strtoupper($this->input('acsee_combination') ?? '');
            $combinationsMap = [
                'PCM' => ['Physics', 'Chemistry', 'Mathematics'],
                'PCB' => ['Physics', 'Chemistry', 'Biology'],
                'CBG' => ['Chemistry', 'Biology', 'Geography'],
                'HGL' => ['History', 'Geography', 'Language'],
                'HKL' => ['History', 'Kiswahili', 'Language'],
                'ECA' => ['Economics', 'Commerce', 'Accountancy'],
                'EGM' => ['Economics', 'Geography', 'Mathematics'],
                'HGE' => ['History', 'Geography', 'Economics'],
                'HGK' => ['History', 'Geography', 'Kiswahili'],
                'PGM' => ['Physics', 'Geography', 'Mathematics'],
                'CBA' => ['Chemistry', 'Biology', 'Agriculture'],
                'CBN' => ['Chemistry', 'Biology', 'Nutrition'],
                'KLF' => ['Kiswahili', 'Language', 'French']
            ];

            if ($combination && isset($combinationsMap[$combination])) {
                $subjects = $combinationsMap[$combination];
                $this->merge([
                    'acsee_subject1' => $this->input('acsee_subject1') ?: $subjects[0],
                    'acsee_subject2' => $this->input('acsee_subject2') ?: $subjects[1],
                    'acsee_subject3' => $this->input('acsee_subject3') ?: $subjects[2],
                ]);
            }

            if ($grade1 && $grade2 && $grade3) {
                $pointsMap = [
                    'A' => 1,
                    'B' => 2,
                    'C' => 3,
                    'D' => 4,
                    'E' => 5,
                    'S' => 6,
                    'F' => 7,
                ];

                $sum = ($pointsMap[$grade1] ?? 0) +
                       ($pointsMap[$grade2] ?? 0) +
                       ($pointsMap[$grade3] ?? 0);

                $this->merge([
                    'acsee_points' => $sum,
                ]);
            }
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $admissionType = $this->input('admission_type');
            $programmeId = $this->input('programme_id');

            if (!$programmeId) {
                return;
            }

            $programme = \App\Models\Programme::find($programmeId);
            if (!$programme) {
                return;
            }

            if ($admissionType === 'Diploma') {
                $gpa = $this->input('gpa');
                if ($gpa !== null) {
                    $gpa = (float) $gpa;
                    if ($gpa < 2.0) {
                        $validator->errors()->add('gpa', 'Kiwango cha chini cha GPA kinachohitajika ili kuomba udahili ni 2.0. (A minimum GPA of 2.0 is required to apply for any programme.)');
                    } elseif ($gpa >= 2.0 && $gpa < 3.0) {
                        if ($programme->code !== 'Foundation') {
                            $validator->errors()->add('programme_id', 'Kwa GPA ya 2.0 hadi 2.9, una sifa za kujiunga na Foundation Course pekee. (With a GPA between 2.0 and 2.9, you only qualify for the Foundation Course bridging programme.)');
                        }
                    } else {
                        // gpa >= 3.0
                        if ($programme->code === 'Foundation') {
                            $validator->errors()->add('programme_id', 'Kwa GPA ya 3.0 au zaidi, una sifa za kujiunga na Shahada (Bachelor Degree) au Uzamili. Tafadhali chagua programu ya shahada. (With a GPA of 3.0 or above, you qualify for direct degree entry. Please choose a Bachelor or Postgraduate programme.)');
                        }
                    }
                }
            } elseif ($admissionType === 'Form Six') {
                $points = $this->input('acsee_points');
                $g1 = $this->input('acsee_grade1');
                $g2 = $this->input('acsee_grade2');
                $g3 = $this->input('acsee_grade3');

                $calculator = app(\App\Services\AdmissionCategoryCalculatorService::class);
                $category = $calculator->calculate($admissionType, null, $points ? (float) $points : null, $g1, $g2, $g3);

                if ($category === 'Foundation') {
                    if ($programme->code !== 'Foundation') {
                        $validator->errors()->add('programme_id', 'Kwa sifa zako za kidato cha sita, una sifa za kujiunga na Foundation Course pekee. (Based on your Form Six qualifications, you only qualify for the Foundation Course bridging programme.)');
                    }
                } else {
                    if ($programme->code === 'Foundation') {
                        $validator->errors()->add('programme_id', 'Una sifa za kujiunga na Shahada (Bachelor Degree) au Uzamili. Tafadhali chagua programu ya shahada. (You qualify for direct degree entry. Please choose a Bachelor or Postgraduate programme.)');
                    }
                }
            }
        });
    }
}

