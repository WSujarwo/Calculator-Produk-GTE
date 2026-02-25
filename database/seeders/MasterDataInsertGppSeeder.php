<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataInsertGppSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rowsJson = <<<'JSON'
[{"row_no":5,"category":"Adhesive","form":"Liquid","category_detail":"PTFE Dispersion","special_category":"SFN 1","code_1":"ADV","code_2":"LQ","code_3":"PTF","code_4":"SFN","part_no":"ADV-LQ-PTF-SFN","description":"Adhesive - PTFE Dispersion SFN 1 - Liquid","part_no_lama_1":"BRMIX-FPTFE-G-GTE","part_no_lama_2":"BRMIX-DBAS4-G-GTE","part_no_lama_3":"POW- BARIUM SULFAT"},{"row_no":6,"category":"Lubricant / Wax","form":"Liquid","category_detail":"Silicone Oil","special_category":"KF-96-1000CS","code_1":"WAX","code_2":"LQ","code_3":"SLC","code_4":"K96","part_no":"WAX-LQ-SLC-K96","description":"Lubricant / Wax - Silicone Oil KF-96-1000CS - Liquid","part_no_lama_1":"BRMIX-FSLCO-H-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":7,"category":"Lubricant / Wax","form":"Liquid","category_detail":"Silinap Oil","special_category":"Silinap Oil","code_1":"WAX","code_2":"LQ","code_3":"SLA","code_4":"SLA","part_no":"WAX-LQ-SLA-SLA","description":"Lubricant / Wax - Silinap Oil Silinap Oil - Liquid","part_no_lama_1":"BRMIX-FSLNO-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":8,"category":"Solvent","form":"Liquid","category_detail":"Toluene","special_category":"0.998","code_1":"SOL","code_2":"LQ","code_3":"TOL","code_4":"998","part_no":"SOL-LQ-TOL-998","description":"Solvent - Toluene 0.998 - Liquid","part_no_lama_1":"BRMIX-FTOLU-G-GTE","part_no_lama_2":"BRMIX-DCARB-G-GTE","part_no_lama_3":"POWDER-CARBON"},{"row_no":9,"category":"Lubricant / Wax","form":"Liquid","category_detail":"White Oil","special_category":"15","code_1":"WAX","code_2":"LQ","code_3":"WHO","code_4":"015","part_no":"WAX-LQ-WHO-015","description":"Lubricant / Wax - White Oil 15 - Liquid","part_no_lama_1":"BRMIX-FWHOI-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":10,"category":"Adhesive","form":"Pasta","category_detail":"Lem","special_category":"Aibon 601","code_1":"ADV","code_2":"PA","code_3":"LEM","code_4":"AI6","part_no":"ADV-PA-LEM-AI6","description":"Adhesive - Lem Aibon 601 - Pasta","part_no_lama_1":"BRMIX-PA601-G-GTE","part_no_lama_2":"BRMIX-DSONI-G-GTE","part_no_lama_3":"POW-SODIUM NITRITE"},{"row_no":11,"category":"Lubricant / Wax","form":"Pasta","category_detail":"Calcium Grease","special_category":"Pikoli","code_1":"WAX","code_2":"PA","code_3":"CX3","code_4":"PIK","part_no":"WAX-PA-CX3-PIK","description":"Lubricant / Wax - Calcium Grease Pikoli - Pasta","part_no_lama_1":"BRMIX-PCOX3-G-GTE","part_no_lama_2":"BRMIX-DSULF-G-GTE","part_no_lama_3":"POW-SULFUR"},{"row_no":12,"category":"Adhesive","form":"Pasta","category_detail":"Lem","special_category":"Fox C2.11.008","code_1":"ADV","code_2":"PA","code_3":"LEM","code_4":"FC2","part_no":"ADV-PA-LEM-FC2","description":"Adhesive - Lem Fox C2.11.008 - Pasta","part_no_lama_1":"BRMIX-PFOXC-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":13,"category":"Filler / Reinforcing","form":"Powder","category_detail":"Kaolin","special_category":"Mesh 325","code_1":"FIL","code_2":"PO","code_3":"KAO","code_4":"325","part_no":"FIL-PO-KAO-325","description":"Filler / Reinforcing - Kaolin Mesh 325 - Powder","part_no_lama_1":"BRMIX-DCAOL-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":14,"category":"Lubricant / Wax","form":"Powder","category_detail":"Graphite Silver","special_category":"Graphite Silver","code_1":"WAX","code_2":"PO","code_3":"GPS","code_4":"GPS","part_no":"WAX-PO-GPS-GPS","description":"Lubricant / Wax - Graphite Silver Graphite Silver - Powder","part_no_lama_1":"BRMIX-DGRAP-G-GTE","part_no_lama_2":"BRMIX-DZOXI-G-GTE","part_no_lama_3":"POW-ZINC OXIDE"},{"row_no":15,"category":"Lubricant / Wax","form":"Powder","category_detail":"Talk Lioning","special_category":"Talk Lioning","code_1":"WAX","code_2":"PO","code_3":"TLN","code_4":"TLN","part_no":"WAX-PO-TLN-TLN","description":"Lubricant / Wax - Talk Lioning Talk Lioning - Powder","part_no_lama_1":"BRMIX-DTALI-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":16,"category":"Filler / Reinforcing","form":"Powder","category_detail":"Titanium Dioxide","special_category":"KA-100","code_1":"FIL","code_2":"PO","code_3":"TIO","code_4":"100","part_no":"FIL-PO-TIO-100","description":"Filler / Reinforcing - Titanium Dioxide KA-100 - Powder","part_no_lama_1":"BRMIX-DTIDI-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":17,"category":"Lubricant / Wax","form":"Solid","category_detail":"Paraffin Wax","special_category":"Paraffin 60","code_1":"WAX","code_2":"SL","code_3":"PWX","code_4":"P60","part_no":"WAX-SL-PWX-P60","description":"Lubricant / Wax - Paraffin Wax Paraffin 60 - Solid","part_no_lama_1":"BRMIX-SPAWA-G-GTE","part_no_lama_2":"","part_no_lama_3":""},{"row_no":18,"category":"Reinforcement","form":"Wire","category_detail":"Wire SS304","special_category":"Thk. 0.2 mm","code_1":"REI","code_2":"WR","code_3":"S34","code_4":"T02","part_no":"REI-WR-S34-T02","description":"Reinforcement - Wire SS304 Thk. 0.2 mm - Wire","part_no_lama_1":"","part_no_lama_2":"","part_no_lama_3":""},{"row_no":19,"category":"Reinforcement","form":"Wire","category_detail":"Wire Inconel 675","special_category":"Thk. 0.2 mm","code_1":"REI","code_2":"WR","code_3":"I75","code_4":"T02","part_no":"REI-WR-I75-T02","description":"Reinforcement - Wire Inconel 675 Thk. 0.2 mm - Wire","part_no_lama_1":"","part_no_lama_2":"","part_no_lama_3":""}]
JSON;

        $rows = array_map(
            static fn (array $row): array => [
                ...$row,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            json_decode($rowsJson, true, 512, JSON_THROW_ON_ERROR)
        );

        DB::table('master_data_insert_gpp')->upsert(
            $rows,
            ['part_no'],
            [
                'row_no', 'category', 'form', 'category_detail', 'special_category',
                'code_1', 'code_2', 'code_3', 'code_4',
                'description', 'part_no_lama_1', 'part_no_lama_2', 'part_no_lama_3',
                'is_active', 'updated_at'
            ]
        );
    }
}
