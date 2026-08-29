<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('content');
            $table->text('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->dropColumn('file_path');
            $table->text('content')->nullable(false)->change();
        });
    }
};
