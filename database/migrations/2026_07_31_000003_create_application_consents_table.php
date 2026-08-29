<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('privacy_policy_id')->nullable()->constrained('privacy_policies')->onDelete('set null');
            $table->foreignId('terms_conditions_id')->nullable()->constrained('terms_conditions')->onDelete('set null');
            $table->string('consent_version');
            $table->string('consent_language')->default('en');
            $table->string('consent_source')->default('Web');
            $table->string('device_type')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('application_status_at_consent')->nullable();
            $table->boolean('consent_given')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->string('consent_hash', 64)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_consents');
    }
};
