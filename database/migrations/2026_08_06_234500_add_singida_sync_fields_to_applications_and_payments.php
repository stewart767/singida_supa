<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'singida_admission_id')) {
                $table->unsignedBigInteger('singida_admission_id')->nullable()->after('is_public_submission')->index();
            }
            if (! Schema::hasColumn('applications', 'singida_synced_at')) {
                $table->timestamp('singida_synced_at')->nullable()->after('singida_admission_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'singida_synced')) {
                $table->boolean('singida_synced')->default(false)->after('rejection_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            foreach (['singida_admission_id', 'singida_synced_at'] as $column) {
                if (Schema::hasColumn('applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'singida_synced')) {
                $table->dropColumn('singida_synced');
            }
        });
    }
};
