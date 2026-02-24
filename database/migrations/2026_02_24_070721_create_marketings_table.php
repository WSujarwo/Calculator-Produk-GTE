<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
            $table->string('marketing_no')->unique(); // contoh: MKT-001
            $table->string('name');                   // USER MARKETING
            $table->string('email')->unique();
            $table->string('phone')->nullable();      // NO HANDPHONE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketings');
    }
};