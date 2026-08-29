<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->string('control_number')->unique()->comment('99100... control number format');
            $table->decimal('amount', 12, 2)->default(20000.00);
            $table->string('currency', 3)->default('TZS');
            $table->enum('payment_status', ['pending', 'paid', 'rejected'])->default('pending');
            $table->string('receipt_path')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('payment_method')->default('Bank Deposit / Mobile Money');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->onDelete('cascade');
            $table->string('admission_number')->unique()->comment('e.g. SUPA/ADM/2026/00101');
            $table->string('verification_code', 32)->unique();
            $table->string('pdf_path')->nullable();
            $table->string('qr_code_hash')->nullable();
            $table->date('reporting_date')->nullable();
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_letters');
        Schema::dropIfExists('payments');
    }
};
