<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ModulePermissionSeeder::class,
            EjmShapesAndTypesSeeder::class,
            GppValidationSeeder::class,
            TabelPrioritasMesinGppSeeder::class,
            MasterDataBobbinDanPlyGppSeeder::class,
            MasterDataProdukGppSeeder::class,
            MasterDataInsertGppSeeder::class,
            DataInsert2GppSeeder::class,
            RmYarnPer10GrGppSeeder::class,
            DurasiProsesValidationGppSeeder::class,
            DataValidasiEjmActualDesainCalculationSeeder::class,
            DataValidasiEjmExpansionJointSeeder::class,
        ]);
    }
}
