<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $logistik = Role::firstOrCreate(['name' => 'logistik']);
        $ppc = Role::firstOrCreate(['name' => 'ppc']);
        $estimator = Role::firstOrCreate(['name' => 'estimator']);
    
        // ADMIN
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin417661')
            ]
        );
        $adminUser->syncRoles($admin);
    
        // LOGISTIK
        $logistikUser = User::firstOrCreate(
            ['email' => 'logistik@mail.com'],
            [
                'name' => 'Logistik',
                'password' => Hash::make('logistik123')
            ]
        );
        $logistikUser->syncRoles($logistik);
    
        // PPC
        $ppcUser = User::firstOrCreate(
            ['email' => 'ppc@mail.com'],
            [
                'name' => 'PPC',
                'password' => Hash::make('ppc123')
            ]
        );
        $ppcUser->syncRoles($ppc);
    
        // ESTIMATOR
        $estimatorUser = User::firstOrCreate(
            ['email' => 'estimator@mail.com'],
            [
                'name' => 'Estimator',
                'password' => Hash::make('estimator123')
            ]
        );
        $estimatorUser->syncRoles($estimator);
    }
}