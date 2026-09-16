<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\ApplicationWorkflowService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Data remediation: Fix duplicate application numbers iteratively until none remain
        do {
            $duplicateApp = DB::table('applications')
                ->select('application_number')
                ->groupBy('application_number')
                ->havingRaw('count(*) > 1')
                ->first();

            if ($duplicateApp) {
                $apps = DB::table('applications')
                    ->where('application_number', $duplicateApp->application_number)
                    ->orderBy('id', 'asc')
                    ->get();

                $first = true;
                foreach ($apps as $app) {
                    if ($first) {
                        $first = false;
                        continue;
                    }

                    $newAppNumber = ApplicationWorkflowService::generateUniqueApplicationNumber();

                    DB::table('applications')
                        ->where('id', $app->id)
                        ->update([
                            'application_number' => $newAppNumber,
                            'updated_at' => now(),
                        ]);

                    // Payment check & reset if duplicate control number
                    $payment = DB::table('payments')->where('application_id', $app->id)->first();
                    if ($payment) {
                        $sharedControl = DB::table('payments')
                            ->where('control_number', $payment->control_number)
                            ->where('id', '!=', $payment->id)
                            ->where('control_number', 'not like', 'PENDING-%')
                            ->exists();

                        if ($sharedControl) {
                            if (in_array(strtolower((string)$payment->payment_status), ['pending', 'unpaid', 'rejected'], true) || in_array($app->status, ['Draft', 'Pending Payment'], true)) {
                                DB::table('payments')
                                    ->where('id', $payment->id)
                                    ->update([
                                        'control_number' => 'PENDING-' . $app->id,
                                        'singida_synced' => 0,
                                        'updated_at' => now(),
                                    ]);

                                DB::table('applications')
                                    ->where('id', $app->id)
                                    ->update([
                                        'singida_admission_id' => null,
                                        'singida_synced_at' => null,
                                    ]);
                            }
                        }
                    }
                }
            }
        } while ($duplicateApp);

        // Reset any remaining duplicate control numbers where one of them is draft/pending
        $dupePayments = DB::table('payments')
            ->select('control_number')
            ->where('control_number', 'not like', 'PENDING-%')
            ->groupBy('control_number')
            ->havingRaw('count(*) > 1')
            ->pluck('control_number');

        foreach ($dupePayments as $controlNum) {
            $pays = DB::table('payments')
                ->join('applications', 'payments.application_id', '=', 'applications.id')
                ->where('payments.control_number', $controlNum)
                ->select('payments.id as pid', 'payments.application_id', 'payments.payment_status', 'applications.status as app_status')
                ->get();

            foreach ($pays as $p) {
                if (in_array(strtolower((string)$p->payment_status), ['pending', 'unpaid', 'rejected'], true) || in_array($p->app_status, ['Draft', 'Pending Payment'], true)) {
                    DB::table('payments')->where('id', $p->pid)->update([
                        'control_number' => 'PENDING-' . $p->application_id,
                        'singida_synced' => 0,
                        'updated_at' => now(),
                    ]);
                    DB::table('applications')->where('id', $p->application_id)->update([
                        'singida_admission_id' => null,
                        'singida_synced_at' => null,
                    ]);
                }
            }
        }

        // 2. Add UNIQUE constraint to applications.application_number
        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->unique('application_number', 'applications_application_number_unique');
            });
        } catch (\Throwable $e) {
            // Unique index already present
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropUnique('applications_application_number_unique');
            });
        } catch (\Throwable $e) {
            // Index not present
        }
    }
};
