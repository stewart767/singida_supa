<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('BAED, BSCED, IMPTE, Foundation, etc.');
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('faculty')->nullable();
            $table->text('description')->nullable();
            $table->text('entry_requirements')->nullable();
            $table->integer('duration_years')->default(3);
            $table->decimal('annual_fee', 12, 2)->default(0);
            $table->decimal('monthly_fee', 12, 2)->default(0);
            $table->decimal('application_fee', 12, 2)->default(20000.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
