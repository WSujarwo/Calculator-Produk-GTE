<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class DataValidasiEjmProsesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        $rows = [];

        // Helper NB list (common series)
        $nbSeries = [100,125,150,200,250,300,350,400,450,500,550,600,650,700,750,800];

        //
        // Bellows - Cutting Shearing
        //
        $bellows_cutting = [
            100=>30,125=>30,150=>30,200=>30,
            250=>40,300=>40,350=>40,400=>50,
            450=>50,500=>50,550=>50,600=>60,
            650=>60,700=>60,750=>60,800=>60,
        ];
        foreach ($bellows_cutting as $nb => $val) {
            $rows[] = [
                'component_type' => 'Bellows',
                'process_name'   => 'Cutting Shearing',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'menit',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Bellows - Rolling
        //
        $bellows_rolling = [
            100=>15,125=>15,150=>15,200=>15,
            250=>20,300=>20,350=>20,400=>25,
            450=>25,500=>25,550=>25,600=>30,
            650=>30,700=>30,750=>30,800=>30,
        ];
        foreach ($bellows_rolling as $nb => $val) {
            $rows[] = [
                'component_type' => 'Bellows',
                'process_name'   => 'Rolling',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Bellows - Seam Welding
        //
        $bellows_seam = [
            100=>35,125=>35,150=>35,200=>35,
            250=>45,300=>45,350=>45,400=>55,
            450=>55,500=>55,550=>55,600=>65,
            650=>65,700=>65,750=>65,800=>65,
        ];
        foreach ($bellows_seam as $nb => $val) {
            $rows[] = [
                'component_type' => 'Bellows',
                'process_name'   => 'Seam Welding',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Bellows - Hydro Forming
        //
        $bellows_hydro = [
            100=>35,125=>35,150=>35,200=>35,
            250=>45,300=>45,350=>45,400=>55,
            450=>55,500=>55,550=>55,600=>65,
            650=>65,700=>65,750=>65,800=>65,
        ];
        foreach ($bellows_hydro as $nb => $val) {
            $rows[] = [
                'component_type' => 'Bellows',
                'process_name'   => 'Hydro Forming',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Collar - Cutting Shearing
        //
        $collar_cutting = [
            100=>8,125=>8,150=>8,200=>8,
            250=>10,300=>10,350=>10,400=>10,
            450=>12,500=>12,550=>12,600=>12,
            650=>14,700=>14,750=>14,800=>14,
        ];
        foreach ($collar_cutting as $nb => $val) {
            $rows[] = [
                'component_type' => 'Collar',
                'process_name'   => 'Cutting Shearing',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Collar - Rolling
        //
        $collar_rolling = $collar_cutting; // same values in your sheet
        foreach ($collar_rolling as $nb => $val) {
            $rows[] = [
                'component_type' => 'Collar',
                'process_name'   => 'Rolling',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Collar - Welding
        //
        $collar_welding = [
            100=>25,125=>25,150=>25,200=>25,
            250=>30,300=>30,350=>30,400=>30,
            450=>35,500=>35,550=>35,600=>35,
            650=>40,700=>40,750=>40,800=>40,
        ];
        foreach ($collar_welding as $nb => $val) {
            $rows[] = [
                'component_type' => 'Collar',
                'process_name'   => 'Welding',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => $val,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Pipe End - Cutting
        //
        $pipe_cutting = [
            100=>5,125=>5,150=>5,200=>5,
            250=>10,300=>10,350=>10,400=>10,
            450=>15,500=>15,550=>15,600=>15,
            650=>20,700=>20,750=>20,800=>20,
        ];
        foreach ($pipe_cutting as $nb => $val) {
            $rows[] = [
                'component_type' => 'Pipe End',
                'process_name'   => 'Cutting',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Pipe End - Bevel
        //
        $pipe_bevel = [
            100=>10,125=>10,150=>10,200=>10,
            250=>15,300=>15,350=>15,400=>15,
            450=>20,500=>20,550=>20,600=>20,
            650=>25,700=>25,750=>25,800=>25,
        ];
        foreach ($pipe_bevel as $nb => $val) {
            $rows[] = [
                'component_type' => 'Pipe End',
                'process_name'   => 'Bevel',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Pipe End - Grinding
        //
        $pipe_grinding = [
            100=>10,125=>10,150=>10,200=>10,
            250=>20,300=>20,350=>20,400=>20,
            450=>30,500=>30,550=>30,600=>30,
            650=>35,700=>35,750=>35,800=>35,
        ];
        foreach ($pipe_grinding as $nb => $val) {
            $rows[] = [
                'component_type' => 'Pipe End',
                'process_name'   => 'Grinding',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        //
        // Generic groups (Cutting Shearing / Rolling / Welding large table from your later section)
        //
        $generic_cutting = [
            100=>60,125=>60,150=>60,200=>60,
            250=>90,300=>90,350=>90,400=>90,
            450=>120,500=>120,550=>120,600=>120,
            650=>120,700=>150,750=>150,800=>150,
        ];
        foreach ($generic_cutting as $nb => $val) {
            $rows[] = [
                'component_type' => 'Generic',
                'process_name'   => 'Cutting Shearing',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $generic_rolling = [
            100=>30,125=>30,150=>30,200=>30,
            250=>45,300=>45,350=>45,400=>45,
            450=>60,500=>60,550=>60,600=>60,
            650=>60,700=>90,750=>90,800=>90,
        ];
        foreach ($generic_rolling as $nb => $val) {
            $rows[] = [
                'component_type' => 'Generic',
                'process_name'   => 'Rolling',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $generic_welding = [
            100=>50,125=>50,150=>50,200=>50,
            250=>75,300=>75,350=>75,400=>75,
            450=>90,500=>90,550=>90,600=>90,
            650=>90,700=>120,750=>120,800=>120,
        ];
        foreach ($generic_welding as $nb => $val) {
            $rows[] = [
                'component_type' => 'Generic',
                'process_name'   => 'Welding',
                'nb'             => $nb,
                'tube_inner'     => $val,
                'price_tube_inner' => null,
                'tube_outer'     => null,
                'price_tube_outer' => null,
                'unit' => 'mm',
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks
        $chunks = array_chunk($rows, 200);
        foreach ($chunks as $chunk) {
            DB::table('data_validasiejm_proses')->insert($chunk);
        }

        $this->command->info('Inserted '.count($rows).' rows into data_validasiejm_proses');
    }
}