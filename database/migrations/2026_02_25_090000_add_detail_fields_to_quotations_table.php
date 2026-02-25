<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedInteger('revision_no')->default(0)->after('quotation_date');
            $table->string('attention')->nullable()->after('marketing_id');
            $table->string('delivery_to')->nullable()->after('attention');
            $table->string('delivery_term')->nullable()->after('delivery_to');
            $table->unsignedInteger('payment_days')->nullable()->after('delivery_term');
            $table->unsignedInteger('delivery_time_days')->nullable()->after('payment_days');
            $table->string('scope_of_work')->nullable()->after('delivery_time_days');
            $table->unsignedInteger('price_validity_weeks')->nullable()->after('scope_of_work');
            $table->enum('result_status', ['GAGAL', 'PENDING', 'SUKSES'])->default('PENDING')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'revision_no',
                'attention',
                'delivery_to',
                'delivery_term',
                'payment_days',
                'delivery_time_days',
                'scope_of_work',
                'price_validity_weeks',
                'result_status',
            ]);
        });
    }
};
