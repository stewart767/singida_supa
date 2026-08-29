<?php

namespace App\Services;

use App\Models\Setting;

class AdmissionCategoryCalculatorService
{
    /**
     * Automatically calculate the admission category based on configurable business rules.
     */
    public function calculate(string $admissionType, ?float $gpa, ?float $acseePoints, ?string $g1 = null, ?string $g2 = null, ?string $g3 = null): string
    {
        $minDirectEntryGpa = (float) Setting::get('direct_entry_min_gpa', 3.0);
        $minDirectEntryPoints = (float) Setting::get('direct_entry_min_points', 5);

        if ($admissionType === 'Diploma') {
            if ($gpa !== null && $gpa >= $minDirectEntryGpa) {
                return 'Direct Entry';
            }
            return 'Foundation';
        }

        if ($admissionType === 'Form Six') {
            // Check based on NECTA subject grades (at least two principal passes: A, B, C, D, E)
            if (!empty($g1) || !empty($g2) || !empty($g3)) {
                $principalCount = 0;
                $principalGrades = ['A', 'B', 'C', 'D', 'E'];
                
                if ($g1 && in_array(strtoupper($g1), $principalGrades)) $principalCount++;
                if ($g2 && in_array(strtoupper($g2), $principalGrades)) $principalCount++;
                if ($g3 && in_array(strtoupper($g3), $principalGrades)) $principalCount++;
                
                if ($principalCount >= 2) {
                    return 'Direct Entry';
                }
                return 'Foundation';
            }

            // Fallback for points only (NECTA scale: 3 to 21, where lower is better. <= 17 points is Direct Entry)
            if ($acseePoints !== null) {
                if ($acseePoints <= 17) {
                    return 'Direct Entry';
                }
                return 'Foundation';
            }
            return 'Foundation';
        }

        return 'Direct Entry';
    }
}
