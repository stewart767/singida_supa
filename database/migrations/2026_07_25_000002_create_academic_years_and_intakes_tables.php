<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('e.g. 2026/2027');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('application_deadline');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('September, January, March');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intakes');
        Schema::dropIfExists('academic_years');
    }
};
