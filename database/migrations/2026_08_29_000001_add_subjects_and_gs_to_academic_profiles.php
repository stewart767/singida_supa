<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_profiles', function (Blueprint $table) {
            $table->string('acsee_subject1', 100)->nullable()->after('acsee_combination');
            $table->string('acsee_subject2', 100)->nullable()->after('acsee_grade1');
            $table->string('acsee_subject3', 100)->nullable()->after('acsee_grade2');
            $table->string('acsee_gs_grade', 5)->nullable()->after('acsee_grade3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_profiles', function (Blueprint $table) {
            $table->dropColumn(['acsee_subject1', 'acsee_subject2', 'acsee_subject3', 'acsee_gs_grade']);
        });
    }
};
