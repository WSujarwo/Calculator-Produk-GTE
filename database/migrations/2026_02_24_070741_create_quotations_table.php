<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->string('quotation_no')->unique();
            $table->date('quotation_date');

            // Relasi
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->cascadeOnDelete();

            $table->foreignId('marketing_id')
                  ->constrained('marketings')
                  ->cascadeOnDelete();

            // Snapshot address (optional, supaya tidak berubah kalau company edit)
            $table->text('company_address')->nullable();

            $table->enum('status', [
                'DRAFT',
                'SUBMITTED',
                'APPROVED',
                'REJECTED',
                'CANCELLED'
            ])->default('DRAFT');

            $table->foreignId('created_by')->nullable(); // nanti bisa relasi users
            $table->foreignId('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};