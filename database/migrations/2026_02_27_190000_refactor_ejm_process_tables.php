<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $oldTable = 'data_validasiejm_proses';
    private string $definitions = 'ejm_process_definitions';
    private string $times = 'ejm_process_times';

    public function up(): void
    {
        if (! Schema::hasTable($this->definitions)) {
            Schema::create($this->definitions, function (Blueprint $table) {
                $table->id();
                $table->string('component_type', 80)->index();
                $table->string('process_name', 120)->index();
                $table->unsignedSmallInteger('sequence')->default(1);
                $table->boolean('has_inner_outer')->default(true);
                $table->decimal('rate_inner_per_hour', 12, 2)->nullable();
                $table->decimal('rate_outer_per_hour', 12, 2)->nullable();
                $table->string('currency', 10)->default('IDR');
                $table->string('unit', 20)->nullable()->default('menit');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['component_type', 'process_name'], 'uq_ejm_process_definitions_name');
            });
        }

        if (! Schema::hasTable($this->times)) {
            Schema::create($this->times, function (Blueprint $table) {
                $table->id();
                $table->foreignId('process_definition_id')->constrained($this->definitions)->cascadeOnDelete();
                $table->unsignedInteger('nb')->nullable()->index();
                $table->unsignedInteger('noc')->nullable()->index();
                $table->decimal('minutes_inner', 10, 2)->nullable();
                $table->decimal('minutes_outer', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['process_definition_id', 'nb', 'noc'], 'uq_ejm_process_times_lookup');
            });
        }

        if (Schema::hasTable($this->oldTable)) {
            $oldRows = DB::table($this->oldTable)->orderBy('id')->get();

            foreach ($oldRows as $row) {
                $componentType = trim((string) ($row->component_type ?? ''));
                $processName = trim((string) ($row->process_name ?? ''));
                if ($componentType === '' || $processName === '') {
                    continue;
                }

                $definition = DB::table($this->definitions)
                    ->where('component_type', $componentType)
                    ->where('process_name', $processName)
                    ->first();

                $rateInner = $row->price_tube_inner;
                $rateOuter = $row->price_tube_outer;
                if ($definition) {
                    DB::table($this->definitions)
                        ->where('id', $definition->id)
                        ->update([
                            'has_inner_outer' => ($row->tube_outer !== null),
                            'rate_inner_per_hour' => $rateInner,
                            'rate_outer_per_hour' => $rateOuter,
                            'unit' => $row->unit ?? 'menit',
                            'notes' => $row->notes,
                            'updated_at' => now(),
                        ]);
                    $definitionId = (int) $definition->id;
                } else {
                    $maxSeq = (int) DB::table($this->definitions)
                        ->where('component_type', $componentType)
                        ->max('sequence');

                    $definitionId = (int) DB::table($this->definitions)->insertGetId([
                        'component_type' => $componentType,
                        'process_name' => $processName,
                        'sequence' => $maxSeq + 1,
                        'has_inner_outer' => ($row->tube_outer !== null),
                        'rate_inner_per_hour' => $rateInner,
                        'rate_outer_per_hour' => $rateOuter,
                        'currency' => 'IDR',
                        'unit' => $row->unit ?? 'menit',
                        'notes' => $row->notes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $timeQuery = DB::table($this->times)->where('process_definition_id', $definitionId);
                if ($row->nb === null) {
                    $timeQuery->whereNull('nb');
                } else {
                    $timeQuery->where('nb', (int) $row->nb);
                }
                $timeQuery->whereNull('noc');

                $existingTime = $timeQuery->first();
                $timePayload = [
                    'minutes_inner' => $row->tube_inner,
                    'minutes_outer' => $row->tube_outer,
                    'notes' => $row->notes,
                    'updated_at' => now(),
                ];

                if ($existingTime) {
                    DB::table($this->times)->where('id', $existingTime->id)->update($timePayload);
                } else {
                    DB::table($this->times)->insert(array_merge($timePayload, [
                        'process_definition_id' => $definitionId,
                        'nb' => $row->nb,
                        'noc' => null,
                        'created_at' => now(),
                    ]));
                }
            }

            Schema::dropIfExists($this->oldTable);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->oldTable)) {
            Schema::create($this->oldTable, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('component_type', 80)->index();
                $table->string('process_name', 120)->index();
                $table->integer('nb')->nullable()->index();
                $table->integer('tube_inner')->nullable();
                $table->decimal('price_tube_inner', 12, 2)->nullable();
                $table->integer('tube_outer')->nullable();
                $table->decimal('price_tube_outer', 12, 2)->nullable();
                $table->string('unit', 20)->nullable()->default('mm');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['component_type', 'process_name', 'nb'], 'uq_component_process_nb');
            });
        }

        if (Schema::hasTable($this->times) && Schema::hasTable($this->definitions)) {
            $rows = DB::table($this->times . ' as t')
                ->join($this->definitions . ' as d', 'd.id', '=', 't.process_definition_id')
                ->select([
                    'd.component_type',
                    'd.process_name',
                    't.nb',
                    DB::raw('t.minutes_inner as tube_inner'),
                    DB::raw('d.rate_inner_per_hour as price_tube_inner'),
                    DB::raw('t.minutes_outer as tube_outer'),
                    DB::raw('d.rate_outer_per_hour as price_tube_outer'),
                    'd.unit',
                    DB::raw('COALESCE(t.notes, d.notes) as notes'),
                    't.created_at',
                    't.updated_at',
                ])
                ->get();

            foreach ($rows as $row) {
                DB::table($this->oldTable)->insert((array) $row);
            }
        }

        Schema::dropIfExists($this->times);
        Schema::dropIfExists($this->definitions);
    }
};
