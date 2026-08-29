<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'nida_card_number')) {
                $table->string('nida_card_number', 50)->nullable()->after('nida_number');
            }
            if (!Schema::hasColumn('applicants', 'work_id_number')) {
                $table->string('work_id_number', 50)->nullable()->after('nida_card_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (Schema::hasColumn('applicants', 'nida_card_number')) {
                $table->dropColumn('nida_card_number');
            }
            if (Schema::hasColumn('applicants', 'work_id_number')) {
                $table->dropColumn('work_id_number');
            }
        });
    }
};
