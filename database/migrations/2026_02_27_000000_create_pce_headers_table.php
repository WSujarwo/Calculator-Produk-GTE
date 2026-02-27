<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $hasCompaniesTable = Schema::hasTable('companies');
        $hasMarketingsTable = Schema::hasTable('marketings');

        Schema::create('pce_headers', function (Blueprint $table) use ($hasCompaniesTable, $hasMarketingsTable) {
            $table->bigIncrements('id');

            $table->string('pce_number', 60)->unique();
            $table->string('project_name', 150)->default('Expansion Joint Metal');

            $table->unsignedBigInteger('end_user_id')->nullable();
            $table->string('area', 120)->nullable();

            $table->string('drawing_no', 80)->nullable();
            $table->string('document_no', 80)->nullable();
            $table->string('revision', 20)->nullable();
            $table->date('pce_date')->nullable();

            $table->unsignedBigInteger('sales_user_id')->nullable();

            $table->string('status', 30)->default('PENDING');

            $table->timestamps();

            $table->index('end_user_id', 'idx_pceh_enduser');
            $table->index('sales_user_id', 'idx_pceh_sales');
            $table->index('pce_date', 'idx_pceh_date');
            $table->index('status', 'idx_pceh_status');

            if ($hasCompaniesTable) {
                $table->foreign('end_user_id', 'fk_pceh_enduser')
                    ->references('id')->on('companies')
                    ->nullOnDelete();
            }

            if ($hasMarketingsTable) {
                $table->foreign('sales_user_id', 'fk_pceh_salesuser')
                    ->references('id')->on('marketings')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pce_headers');
    }
};
