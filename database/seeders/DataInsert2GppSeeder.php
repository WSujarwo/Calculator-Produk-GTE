<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataInsert2GppSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rowsJson = <<<'JSON'
[{"row_no":2,"component_raw":"Category","component_group":"Category","data_value":"Filler / Reinforcing","code":"FIL"},{"row_no":3,"component_raw":null,"component_group":"Category","data_value":"Lubricant / Wax","code":"WAX"},{"row_no":4,"component_raw":null,"component_group":"Category","data_value":"Adhesive","code":"ADV"},{"row_no":5,"component_raw":null,"component_group":"Category","data_value":"Solvent","code":"SOL"},{"row_no":6,"component_raw":null,"component_group":"Category","data_value":"Reinforcement","code":"REI"},{"row_no":7,"component_raw":null,"component_group":"Category","data_value":"Curring","code":"CUR"},{"row_no":8,"component_raw":null,"component_group":"Category","data_value":"Anti Corrosion","code":"ANC"},{"row_no":9,"component_raw":"Form","component_group":"Form","data_value":"Solid","code":"SL"},{"row_no":10,"component_raw":null,"component_group":"Form","data_value":"Powder","code":"PO"},{"row_no":11,"component_raw":null,"component_group":"Form","data_value":"Liquid","code":"LQ"},{"row_no":12,"component_raw":null,"component_group":"Form","data_value":"Pasta","code":"PA"},{"row_no":13,"component_raw":null,"component_group":"Form","data_value":"Wire","code":"WR"},{"row_no":14,"component_raw":"Filler","component_group":"Filler","data_value":"Calcium Carbonate","code":"CAC"},{"row_no":15,"component_raw":null,"component_group":"Filler","data_value":"Kaolin","code":"KAO"},{"row_no":16,"component_raw":null,"component_group":"Filler","data_value":"Barium Sulfat","code":"BS4"},{"row_no":17,"component_raw":null,"component_group":"Filler","data_value":"Titanium Dioxide","code":"TIO"},{"row_no":18,"component_raw":"Special Filler","component_group":"Special Filler","data_value":"BT-1000","code":"BT1"},{"row_no":19,"component_raw":null,"component_group":"Special Filler","data_value":"Mesh 325","code":"325"},{"row_no":20,"component_raw":null,"component_group":"Special Filler","data_value":"Barium Sulfat","code":"BS4"},{"row_no":21,"component_raw":null,"component_group":"Special Filler","data_value":"KA-100","code":"100"},{"row_no":22,"component_raw":"Anti Corrosion","component_group":"Anti Corrosion","data_value":"Sodium Nitrite","code":"SN3"},{"row_no":23,"component_raw":"Special Anti Corrosion","component_group":"Special Anti Corrosion","data_value":"145 um","code":"145"},{"row_no":24,"component_raw":"Lubricant / Wax","component_group":"Lubricant / Wax","data_value":"Paraffin Wax","code":"PWX"},{"row_no":25,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"Talk Lioning","code":"TLN"},{"row_no":26,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"Graphite Silver","code":"GPS"},{"row_no":27,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"Calcium Grease","code":"CX3"},{"row_no":28,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"White Oil","code":"WHO"},{"row_no":29,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"Silinap Oil","code":"SLA"},{"row_no":30,"component_raw":null,"component_group":"Lubricant / Wax","data_value":"Silicone Oil","code":"SLC"},{"row_no":31,"component_raw":"Special Lubricant / Wax","component_group":"Special Lubricant / Wax","data_value":"Paraffin 60","code":"P60"},{"row_no":32,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"Talk Lioning","code":"TLN"},{"row_no":33,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"Graphite Silver","code":"GPS"},{"row_no":34,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"Pikoli","code":"PIK"},{"row_no":35,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"15","code":"015"},{"row_no":36,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"Silinap Oil","code":"SLA"},{"row_no":37,"component_raw":null,"component_group":"Special Lubricant / Wax","data_value":"KF-96-1000CS","code":"K96"},{"row_no":38,"component_raw":"Binder / Adhesive ","component_group":"Binder / Adhesive ","data_value":"Lem","code":"LEM"},{"row_no":39,"component_raw":null,"component_group":"Binder / Adhesive ","data_value":"PTFE Dispersion","code":"PTF"},{"row_no":40,"component_raw":"Special Binder /  Adhesive","component_group":"Special Binder /  Adhesive","data_value":"Aibon 601","code":"AI6"},{"row_no":41,"component_raw":null,"component_group":"Special Binder /  Adhesive","data_value":"Fox C2.11.008","code":"FC2"},{"row_no":42,"component_raw":null,"component_group":"Special Binder /  Adhesive","data_value":"SFN 1","code":"SFN"},{"row_no":43,"component_raw":"Solvent","component_group":"Solvent","data_value":"Toluene","code":"TOL"},{"row_no":44,"component_raw":"Special Solvent","component_group":"Special Solvent","data_value":"99.80%","code":"998"},{"row_no":45,"component_raw":"Reinforcement","component_group":"Reinforcement","data_value":"Carbon","code":"CAR"},{"row_no":46,"component_raw":null,"component_group":"Reinforcement","data_value":"Wire SS304","code":"S34"},{"row_no":47,"component_raw":null,"component_group":"Reinforcement","data_value":"Wire Inconel 675","code":"I75"},{"row_no":48,"component_raw":"Special Reinforcement","component_group":"Special Reinforcement","data_value":"Thk. 0.2 mm","code":"T02"},{"row_no":49,"component_raw":"Curring Agent","component_group":"Curring Agent","data_value":"Sulfur","code":"SUL"},{"row_no":50,"component_raw":null,"component_group":"Curring Agent","data_value":"Zinc Oxide","code":"ZNO"}]
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

        DB::table('data_insert_2_gpp')->upsert(
            $rows,
            ['row_no'],
            ['component_raw', 'component_group', 'data_value', 'code', 'is_active', 'updated_at']
        );
    }
}
