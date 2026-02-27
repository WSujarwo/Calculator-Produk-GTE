<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pce_headers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('pce_number', 60)->unique();
            $table->string('project_of_name', 150)->default('Expansion Joint Metal');

            // End User mengacu ke master companies
            $table->unsignedBigInteger('end_user_id')->nullable();
            $table->string('area', 120)->nullable();

            $table->string('drawing_no', 80)->nullable();
            $table->string('document_no', 80)->nullable();
            $table->string('revision', 20)->nullable();
            $table->date('date')->nullable();

            // Sales mengacu ke master marketings
            $table->unsignedBigInteger('sales_id')->nullable();

            // header-level status (dokumen)
            $table->string('status', 30)->default('DRAFT')->index();

            $table->timestamps();

            // short indexes (aman untuk MySQL)
            $table->index('end_user_id', 'idx_pceh_enduser');
            $table->index('sales_id', 'idx_pceh_sales');
            $table->index('date', 'idx_pceh_date');

            // FK (aktif kalau tabelnya ada & namanya sama)
            $table->foreign('end_user_id', 'fk_pceh_enduser')
                ->references('id')->on('companies')
                ->nullOnDelete();

            $table->foreign('sales_id', 'fk_pceh_sales')
                ->references('id')->on('marketings')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pce_headers');
    }
};
