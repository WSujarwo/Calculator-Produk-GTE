<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EjmShapesAndTypesSeeder extends Seeder
{
    public function run(): void
    {
        // 1) PRODUCT: EJM
        DB::table('products')->updateOrInsert(
            ['product_code' => 'EJM'],
            [
                'product_name' => 'Expansion Joint Metal (EJM)',
                'is_active' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $ejmId = DB::table('products')->where('product_code', 'EJM')->value('id');

        // 2) SHAPES
        DB::table('shapes')->updateOrInsert(
            ['shape_code' => 'CIRCULAR'],
            [
                'shape_name' => 'Circular',
                'is_active' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('shapes')->updateOrInsert(
            ['shape_code' => 'RECTANGULAR'],
            [
                'shape_name' => 'Rectangular',
                'is_active' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $circularId    = DB::table('shapes')->where('shape_code', 'CIRCULAR')->value('id');
        $rectangularId = DB::table('shapes')->where('shape_code', 'RECTANGULAR')->value('id');

        // 3) PRODUCT_SHAPES
        DB::table('product_shapes')->updateOrInsert(
            ['product_id' => $ejmId, 'shape_id' => $circularId],
            ['is_active' => 1, 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('product_shapes')->updateOrInsert(
            ['product_id' => $ejmId, 'shape_id' => $rectangularId],
            ['is_active' => 1, 'updated_at' => now(), 'created_at' => now()]
        );

        // 4) TYPES - Circular
        $circularTypes = [
            ['type_code' => 'SINGLE_UNREINFORCED_UNTIED',   'type_name' => 'Single Unreinforced-Untied Expansion Joint'],
            ['type_code' => 'SINGLE_REINFORCED',            'type_name' => 'Single Reinforced Expansion Joint'],
            ['type_code' => 'SINGLE_UNREINFORCED_TIED',     'type_name' => 'Single Unreinforced-Tied Expansion Joint'],
            ['type_code' => 'UNIVERSAL_UNREINFORCED_UNTIED','type_name' => 'Universal Unreinforced-Untied Expansion Joint'],
            ['type_code' => 'UNIVERSAL_REINFORCED',         'type_name' => 'Universal Reinforced Expansion Joint'],
            ['type_code' => 'UNIVERSAL_UNREINFORCED_TIED',  'type_name' => 'Universal Unreinforced-Tied Expansion Joint'],
        ];

        foreach ($circularTypes as $i => $t) {
            DB::table('product_type_configs')->updateOrInsert(
                [
                    'product_id' => $ejmId,
                    'shape_id'   => $circularId,
                    'type_code'  => $t['type_code'],
                ],
                [
                    'type_name'  => $t['type_name'],
                    'sort_order' => $i + 1,
                    'is_active'  => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 5) TYPES - Rectangular
        $rectTypes = [
            ['type_code' => 'SINGLE_UNREINFORCED_U_SHAPED',    'type_name' => 'Single Unreinforced U-shaped'],
            ['type_code' => 'SINGLE_UNREINFORCED_V_SHAPED',    'type_name' => 'Single Unreinforced V-shaped'],
            ['type_code' => 'UNIVERSAL_UNREINFORCED_U_SHAPED', 'type_name' => 'Universal Unreinforced U-shaped'],
            ['type_code' => 'UNIVERSAL_UNREINFORCED_V_SHAPED', 'type_name' => 'Universal Unreinforced V-shaped'],
        ];

        foreach ($rectTypes as $i => $t) {
            DB::table('product_type_configs')->updateOrInsert(
                [
                    'product_id' => $ejmId,
                    'shape_id'   => $rectangularId,
                    'type_code'  => $t['type_code'],
                ],
                [
                    'type_name'  => $t['type_name'],
                    'sort_order' => $i + 1,
                    'is_active'  => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}