<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('pce_items', 'quantity')) {
            Schema::table('pce_items', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1)->after('line_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pce_items', 'quantity')) {
            Schema::table('pce_items', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
