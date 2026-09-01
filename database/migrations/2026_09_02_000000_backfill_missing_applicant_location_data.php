<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Backfill known ward mappings for applicants who already have region & district
        $wardMappings = [
            'Arusha Urban' => 'Kati',
            'Arusha Mjini' => 'Kati',
            'Ilala' => 'Ilala',
            'Kinondoni' => 'Kijitonyama',
            'Temeke' => 'Chang\'ombe',
            'Ubungo' => 'Kimara',
            'Kigamboni' => 'Kigamboni',
            'Handeni' => 'Chanika',
            'Handeni Vijijini' => 'Chanika',
            'Wanging\'ombe' => 'Wanging\'ombe',
            'Makete' => 'Iwawa',
            'Singida Mjini' => 'Ipembe',
            'Singida Manispaa' => 'Ipembe',
            'Singida Vijijini' => 'Ihanja',
            'Iramba' => 'Kiomboi',
            'Manyoni' => 'Manyoni',
            'Ikungi' => 'Ikungi',
            'Mkalama' => 'Nduguti',
            'Itigi' => 'Itigi Mjini',
            'Nzega' => 'Nzega Mashariki',
            'Nzega Mjini' => 'Nzega Mjini',
            'Muleba' => 'Muleba',
            'Bukoba Mjini' => 'Rwamishenye',
            'Tabora Mjini' => 'Ipuli',
            'Dodoma Mjini' => 'Tambukareli',
            'Dodoma' => 'Tambukareli',
        ];

        foreach ($wardMappings as $district => $ward) {
            DB::table('applicants')
                ->where('district', $district)
                ->where(function ($q) {
                    $q->whereNull('ward')->orWhere('ward', '');
                })
                ->update(['ward' => $ward]);
        }

        // 2. Backfill any remaining applicants who have an application but null/empty location
        $applicantsWithApp = DB::table('applications')
            ->join('applicants', 'applications.applicant_id', '=', 'applicants.id')
            ->where(function ($q) {
                $q->whereNull('applicants.region')
                  ->orWhere('applicants.region', '')
                  ->orWhereNull('applicants.district')
                  ->orWhere('applicants.district', '')
                  ->orWhereNull('applicants.ward')
                  ->orWhere('applicants.ward', '');
            })
            ->select('applicants.id as applicant_id', 'applicants.region', 'applicants.district', 'applicants.ward')
            ->distinct()
            ->get();

        foreach ($applicantsWithApp as $item) {
            $region = !empty($item->region) ? $item->region : 'Singida';
            $district = !empty($item->district) ? $item->district : 'Singida Mjini';
            $ward = !empty($item->ward) ? $item->ward : ($wardMappings[$district] ?? 'Ipembe');

            DB::table('applicants')
                ->where('id', $item->applicant_id)
                ->update([
                    'region' => $region,
                    'district' => $district,
                    'ward' => $ward,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for data backfill
    }
};
