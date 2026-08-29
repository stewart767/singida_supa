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
            $table->string('acsee_grade1', 5)->nullable()->after('acsee_combination');
            $table->string('acsee_grade2', 5)->nullable()->after('acsee_grade1');
            $table->string('acsee_grade3', 5)->nullable()->after('acsee_grade2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_profiles', function (Blueprint $table) {
            $table->dropColumn(['acsee_grade1', 'acsee_grade2', 'acsee_grade3']);
        });
    }
};
