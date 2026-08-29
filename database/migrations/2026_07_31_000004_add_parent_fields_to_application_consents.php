<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_consents', function (Blueprint $table) {
            $table->boolean('parent_consent_given')->default(false)->after('consent_given');
            $table->string('parent_name')->nullable()->after('parent_consent_given');
            $table->string('parent_signature')->nullable()->after('parent_name');
            $table->timestamp('parent_consented_at')->nullable()->after('parent_signature');
        });
    }

    public function down(): void
    {
        Schema::table('application_consents', function (Blueprint $table) {
            $table->dropColumn(['parent_consent_given', 'parent_name', 'parent_signature', 'parent_consented_at']);
        });
    }
};
