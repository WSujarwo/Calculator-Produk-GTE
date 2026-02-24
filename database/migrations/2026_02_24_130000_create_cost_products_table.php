<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_products', function (Blueprint $table) {
            $table->id();
            $table->string('dlaborno', 120)->unique();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('glaccount', 80)->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('accountname', 255)->nullable();
            $table->string('statuse', 80)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_products');
    }
};
