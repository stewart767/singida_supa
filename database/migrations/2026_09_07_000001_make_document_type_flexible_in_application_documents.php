<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('application_documents', function (Blueprint $table) {
                $table->string('document_type', 100)->change();
            });
        } catch (\Throwable $e) {
            // Raw DB fallback if doctrine/dbal is not installed for change()
            try {
                DB::statement("ALTER TABLE application_documents MODIFY document_type VARCHAR(100) NOT NULL");
            } catch (\Throwable $e2) {
                // Ignore if already string or unsupported
            }
        }
    }

    public function down(): void
    {
        // No need to revert to strict enum
    }
};
