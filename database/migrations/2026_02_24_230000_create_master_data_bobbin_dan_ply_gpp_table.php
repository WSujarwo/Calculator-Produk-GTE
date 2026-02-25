<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function excelColumns(): array
    {
        $columns = range('a', 'z');
        foreach (range('a', 'b') as $prefix) {
            foreach (range('a', 'z') as $suffix) {
                $columns[] = $prefix . $suffix;
                if ($prefix . $suffix === 'ba') {
                    break 2;
                }
            }
        }

        return $columns;
    }

    public function up(): void
    {
        Schema::create('master_data_bobbin_dan_ply_gpp', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->unique();

            $table->string('col_a', 120)->nullable();
            $table->string('col_b', 20)->nullable();
            $table->string('col_c', 20)->nullable();
            $table->string('col_d', 180)->nullable();
            $table->string('col_e', 180)->nullable();

            foreach ($this->excelColumns() as $column) {
                if (in_array($column, ['a', 'b', 'c', 'd', 'e'], true)) {
                    continue;
                }
                $table->text('col_' . $column)->nullable();
            }

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('col_d', 'uk_master_data_bobbin_dan_ply_gpp_col_d');
            $table->index(['col_a', 'col_b', 'col_c'], 'idx_master_data_bobbin_dan_ply_gpp_abc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data_bobbin_dan_ply_gpp');
    }
};
