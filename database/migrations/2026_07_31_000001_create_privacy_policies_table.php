<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_policies', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('title');
            $table->text('content');
            $table->date('effective_date')->nullable();
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_policies');
    }
};
