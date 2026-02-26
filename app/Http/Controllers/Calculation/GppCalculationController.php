<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GppCalculationController extends Controller
{
    public function index(Request $request)
    {
        $quotationId = $request->query('quotation_id');
        $currentInput = null;
        if (! empty($quotationId) && is_numeric($quotationId)) {
            $currentInput = ['quotation_id' => (int) $quotationId];
        }

        return view('calculation.gpp', $this->buildViewData(null, $currentInput));
    }

    public function validateInput(Request $request)
    {
        $skipQuotationValidation = $request->boolean('skip_quotation_validation');

        $validator = Validator::make($request->all(), [
            'quotation_id' => $skipQuotationValidation
                ? ['nullable', 'integer', Rule::exists('quotations', 'id')]
                : ['required', 'integer', Rule::exists('quotations', 'id')],
            'type' => [
                'required',
                'string',
                Rule::exists('master_data_density_gpp', 'type_code')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'mesin' => [
                'required',
                'string',
                Rule::exists('durasi_proses_gpp', 'mesin_code')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'size' => ['required', 'string'],
            'berat' => ['required', 'numeric', 'min:0'],
            'kelebihan_pengiriman' => ['required', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $type = (string) $request->input('type');
            $mesin = str_pad((string) $request->input('mesin'), 2, '0', STR_PAD_LEFT);
            $size = str_pad((string) $request->input('size'), 2, '0', STR_PAD_LEFT);

            $isMesinSizeValid = DB::table('durasi_proses_dropdown_mesin_size_gpp')
                ->where('is_active', true)
                ->where('list_type', 'full')
                ->where('mesin_code', $mesin)
                ->where('size_code', $size)
                ->exists();

            if (! $isMesinSizeValid) {
                $validator->errors()->add('size', 'Kombinasi mesin dan size tidak valid.');
            }

            $isTypeSizeValid = DB::table('master_data_density_gpp')
                ->where('is_active', true)
                ->where('type_code', $type)
                ->where('size_code', $size)
                ->exists();

            if (! $isTypeSizeValid) {
                $validator->errors()->add('size', 'Kombinasi type dan size tidak valid.');
            }

            $isPrioritasMesinValid = DB::table('tabel_prioritas_mesin_gpp')
                ->where('is_active', true)
                ->where('type_code', $type)
                ->where('mesin_code', $mesin)
                ->where('size_code', $size)
                ->exists();

            if (! $isPrioritasMesinValid) {
                $validator->errors()->add('size', 'Kombinasi type, mesin, dan size tidak ada di Tabel Prioritas Mesin.');
            }
        });

        $validator->validate();

        $input = [
            'quotation_id' => $request->filled('quotation_id') ? (int) $request->input('quotation_id') : null,
            'skip_quotation_validation' => $skipQuotationValidation,
            'type' => (string) $request->input('type'),
            'mesin' => str_pad((string) $request->input('mesin'), 2, '0', STR_PAD_LEFT),
            'size' => str_pad((string) $request->input('size'), 2, '0', STR_PAD_LEFT),
            'berat' => (float) $request->input('berat'),
            'kelebihan_pengiriman' => (float) $request->input('kelebihan_pengiriman'),
        ];

        $calc = $this->runCalculation($input);

        return view('calculation.gpp', $this->buildViewData($calc, $input))
            ->with('status', 'Perhitungan GPP berhasil dijalankan.');
    }

    private function buildViewData(?array $calc = null, ?array $currentInput = null): array
    {
        $types = DB::table('master_data_density_gpp')
            ->where('is_active', true)
            ->select('type_code')
            ->distinct()
            ->orderBy('type_code')
            ->pluck('type_code');

        $mesins = DB::table('durasi_proses_gpp')
            ->where('is_active', true)
            ->select('mesin_code')
            ->distinct()
            ->orderBy('mesin_code')
            ->pluck('mesin_code');

        $sizes = DB::table('durasi_proses_gpp')
            ->where('is_active', true)
            ->select('size_code')
            ->distinct()
            ->orderBy('size_code')
            ->pluck('size_code');

        $mesinSizeMap = DB::table('durasi_proses_gpp')
            ->where('is_active', true)
            ->select('mesin_code', 'size_code')
            ->orderBy('mesin_code')
            ->orderBy('size_code')
            ->get()
            ->groupBy('mesin_code')
            ->map(fn ($rows) => $rows->pluck('size_code')->values())
            ->toArray();

        $typeMesinSizeMap = DB::table('tabel_prioritas_mesin_gpp')
            ->where('is_active', true)
            ->select('type_code', 'mesin_code', 'size_code')
            ->orderBy('type_code')
            ->orderBy('mesin_code')
            ->orderBy('size_code')
            ->get()
            ->groupBy('type_code')
            ->map(function ($typeRows) {
                return $typeRows
                    ->groupBy('mesin_code')
                    ->map(fn ($mesinRows) => $mesinRows->pluck('size_code')->values())
                    ->toArray();
            })
            ->toArray();

        $defaultInput = [
            'quotation_id' => null,
            'skip_quotation_validation' => false,
            'type' => null,
            'mesin' => null,
            'size' => null,
            'berat' => null,
            'kelebihan_pengiriman' => null,
        ];
        $activeInput = array_merge($defaultInput, $currentInput ?? []);

        $selectedQuotation = null;
        if (! empty($activeInput['quotation_id'])) {
            $selectedQuotation = DB::table('quotations')
                ->select('id', 'quotation_no')
                ->where('id', (int) $activeInput['quotation_id'])
                ->first();
        }

        return [
            'types' => $types,
            'mesins' => $mesins,
            'sizes' => $sizes,
            'mesinSizeMap' => $mesinSizeMap,
            'typeMesinSizeMap' => $typeMesinSizeMap,
            'selectedQuotation' => $selectedQuotation,
            'defaultType' => null,
            'defaultMesin' => null,
            'defaultSize' => null,
            'calc' => $calc,
            'activeInput' => $activeInput,
        ];
    }

    private function runCalculation(array $input): array
    {
        $type = $input['type'];
        $mesin = $input['mesin'];
        $size = $input['size'];
        $berat = $input['berat'];
        $kelebihan = $input['kelebihan_pengiriman'];

        $density = (float) (DB::table('master_data_density_gpp')
            ->where('is_active', true)
            ->where('type_code', $type)
            ->where('size_code', $size)
            ->value('density') ?? 0);

        $sparePanjang = (float) (DB::table('durasi_proses_spare_panjang_gpp')
            ->where('is_active', true)
            ->where('mesin_code', $mesin)
            ->value('spare_m') ?? 0);

        if ($sparePanjang <= 0) {
            $sparePanjang = (float) (DB::table('durasi_proses_gpp')
                ->where('is_active', true)
                ->where('mesin_code', $mesin)
                ->where('size_code', $size)
                ->value('spare_panjang_m') ?? 0);
        }

        $sizeNum = (float) $size;

        $kodeRow = DB::table('master_data_bobbin_dan_ply_gpp')
            ->where('is_active', true)
            ->where('col_a', $type)
            ->where('col_b', $size)
            ->where('col_c', $mesin)
            ->first();

        $kodeBarang = $kodeRow->col_d ?? '-';

        // Updated sheet formula: kelebihan pengiriman mengikuti 20% dari input berat.
        $kelebihanBerat = $berat * 0.20;

        $spareBerat = ($density > 0 && $sizeNum > 0)
            ? (((($sizeNum * $sizeNum) * $sparePanjang * $density) / 1000) + $kelebihanBerat)
            : 0;

        $beratPlusSpare = $berat + $spareBerat;

        $panjangPlusSpare = ($density > 0 && $sizeNum > 0)
            ? (($beratPlusSpare / $density) / ($sizeNum * $sizeNum)) * 1000
            : 0;

        $areaConfigs = [
            // Use quantity ratio (Z:AF over AG) to preserve Excel's exact percentage (e.g. 37.5% displayed as 38%).
            'diagonal_sudut' => ['label' => 'Diagonal Sudut', 'material' => 'col_f', 'percent' => 'col_an', 'qty' => 'col_z', 'ply' => 'col_t', 'bobbin' => 'col_n'],
            'diagonal_tengah' => ['label' => 'Diagonal Tengah', 'material' => 'col_g', 'percent' => 'col_ao', 'qty' => 'col_aa', 'ply' => 'col_u', 'bobbin' => 'col_o'],
            'corner' => ['label' => 'Corner', 'material' => 'col_h', 'percent' => 'col_ap', 'qty' => 'col_ab', 'ply' => 'col_v', 'bobbin' => 'col_p'],
            'core' => ['label' => 'Core', 'material' => 'col_i', 'percent' => 'col_aq', 'qty' => 'col_ac', 'ply' => 'col_t', 'bobbin' => null],
            'inner' => ['label' => 'Inner', 'material' => 'col_k', 'percent' => 'col_as', 'qty' => 'col_af', 'ply' => 'col_y', 'bobbin' => 'col_s'],
            'core_cord' => ['label' => 'Core Cord', 'material' => 'col_j', 'percent' => 'col_ar', 'qty' => 'col_ad', 'ply' => 'col_x', 'bobbin' => 'col_r'],
        ];

        $rmPerMeterMap = DB::table('rm_yarn_per_10_gr_gpp')
            ->where('is_active', true)
            ->pluck('berat_per_meter_gr', 'new_pn');

        $rawMaterials = [];
        $totalRawPercent = 0;
        $totalRawWeight = 0;

        foreach ($areaConfigs as $key => $cfg) {
            $material = (string) ($kodeRow->{$cfg['material']} ?? '0');
            $qtyTotal = (float) ($kodeRow->col_ag ?? 0);
            $qtyArea = (float) ($kodeRow->{$cfg['qty']} ?? 0);
            $percent = $qtyTotal > 0 ? ($qtyArea / $qtyTotal) : $this->parsePercent($kodeRow->{$cfg['percent']} ?? 0);
            $beratKebutuhan = $percent * $beratPlusSpare;
            $beratPerMeter = (float) ($rmPerMeterMap[$material] ?? 0) / 1000;

            $rawMaterials[$key] = [
                'area_key' => $key,
                'area' => $cfg['label'],
                'material' => $material,
                'berat_per_meter_kg' => $beratPerMeter,
                'percent' => $percent,
                'berat_kebutuhan_kg' => $beratKebutuhan,
            ];

            // Total raw material follows sheet SUM(D13:D17)/SUM(E13:E17), excluding Core Cord.
            if ($key !== 'core_cord') {
                $totalRawPercent += $percent;
                $totalRawWeight += $beratKebutuhan;
            }
        }

        $bobbinRows = [];
        $durasiRows = [];
        $plyTimes = DB::table('durasi_proses_bobbin_full_gpp')->where('is_active', true)->pluck('waktu_dtk', 'ply');
        $totalDurasiGulung = 0;
        $bobbinFullWeight = 0.75;

        foreach ($areaConfigs as $key => $cfg) {
            $raw = $rawMaterials[$key];
            $ply = (float) ($kodeRow->{$cfg['ply']} ?? 0);
            $bobbinProses = $cfg['bobbin'] ? (float) ($kodeRow->{$cfg['bobbin']} ?? 0) : 0;

            // Excel has special handling for Core:
            // - Ply follows Diagonal Sudut
            // - Bobbin proses is ratio W/T
            if ($key === 'core') {
                $t = (float) ($kodeRow->col_t ?? 0);
                $w = (float) ($kodeRow->col_w ?? 0);
                $coreMaterial = trim((string) ($kodeRow->col_i ?? '0'));
                $ply = $coreMaterial === '0' ? 0 : $ply;
                $bobbinProses = ($coreMaterial === '0' || $t <= 0) ? 0 : ($w / $t);
            }

            $kebutuhanBobbin = $bobbinProses > 0 ? ($raw['berat_kebutuhan_kg'] / $bobbinProses) : 0;
            $bobbinFull = $bobbinProses > 0 ? floor($kebutuhanBobbin / $bobbinFullWeight) : 0;
            $bobbinNotFull = $bobbinProses > 0 ? round(fmod($kebutuhanBobbin, $bobbinFullWeight), 2) : 0;

            $desc = $bobbinProses > 0
                ? sprintf('%s bobbin full (0.75 kg) + 1 bobbin isi %.2f kg', (int) $bobbinFull, $bobbinNotFull)
                : '0 bobbin full (0.75 kg) + 1 bobbin isi 0 kg';

            if ($key === 'core_cord') {
                $totalBenang = ($bobbinFull > 0 ? $bobbinFullWeight : 0) + $bobbinNotFull;
                $desc = sprintf('total benang yang digunakan adalah %.2f kg', $totalBenang);
            }

            $bobbinRows[$key] = [
                'area_key' => $key,
                'area' => $cfg['label'],
                'ply' => $ply,
                'bobbin_proses' => $bobbinProses,
                // In Excel table D26:D31 this value is already divided by Bobbin Proses.
                'berat_kebutuhan_kg' => $kebutuhanBobbin,
                'bobbin_full' => $bobbinFull,
                'bobbin_not_full' => $bobbinNotFull,
                'description' => $desc,
            ];

            $durasi = null;
            if ($key !== 'core_cord' && $ply > 0 && $bobbinProses > 0 && isset($plyTimes[(int) $ply])) {
                // Match Excel sheet logic: proses selalu menambah 1 run saat kebutuhan > 0,
                // termasuk saat bobbin_not_full = 0.
                $totalBobbinRun = $kebutuhanBobbin > 0 ? ($bobbinFull + 1) : 0;
                $durasi = ($totalBobbinRun * $bobbinProses) * (float) $plyTimes[(int) $ply];
                $totalDurasiGulung += $durasi;
            }

            if ($key === 'inner' && ($durasi === null || $durasi < 0.000001)) {
                $durasi = 0;
            }

            $durasiRows[$key] = [
                'area_key' => $key,
                'area' => $cfg['label'],
                'total_durasi_dtk' => $durasi,
            ];
        }

        $biayaGulungPerDetik = (float) (DB::table('durasi_proses_biaya_gulung_gpp')->where('proses', 'Gulung')->value('biaya_per_detik') ?? 0);
        $ratePerDetikMp = (float) (DB::table('durasi_proses_rate_gpp')->value('rate_per_detik') ?? 0);

        $durasiGulungCalc = [
            'total_durasi_dtk' => $totalDurasiGulung,
            'mesin_cost' => $totalDurasiGulung * $biayaGulungPerDetik,
            'mp_cost' => $totalDurasiGulung * $ratePerDetikMp,
        ];
        $durasiGulungCalc['total_cost'] = $durasiGulungCalc['mesin_cost'] + $durasiGulungCalc['mp_cost'];

        $setupDuration = 1200.0;
        $setupCalc = [
            'total_durasi_dtk' => $setupDuration,
            'mesin_cost' => 0,
            'mp_cost' => $setupDuration * $ratePerDetikMp,
        ];
        $setupCalc['total_cost'] = $setupCalc['mesin_cost'] + $setupCalc['mp_cost'];

        $braidingRef = DB::table('durasi_proses_braiding_per_meter_gpp')->where('mesin_code', $mesin)->first();
        $turunanRef = DB::table('durasi_proses_turunan_mesin_gpp')->where('mesin_code', $mesin)->first();
        $braidingDuration = 0;
        if ($braidingRef) {
            // Match Excel: H42 = waktu_braiding_per_meter * (panjang_plus_spare / panjang_turunan_tiap_mesin)
            $panjangTurunanMesin = (float) ($turunanRef->panjang_m ?? 1);
            $braidingDuration = ((float) $braidingRef->waktu_dtk) * ($panjangPlusSpare / max($panjangTurunanMesin, 0.000001));
        }

        $braidingCalc = [
            'total_durasi_dtk' => $braidingDuration,
            'mesin_cost' => $braidingDuration * (float) ($braidingRef->biaya_per_detik ?? 0),
            'mp_cost' => $braidingDuration * $ratePerDetikMp,
        ];
        $braidingCalc['total_cost'] = $braidingCalc['mesin_cost'] + $braidingCalc['mp_cost'];

        $gpCosts = [];
        $gpRows = DB::table('durasi_proses_gp_gpp')->where('is_active', true)->get()->keyBy('proses');

        foreach (['Press', 'Gulung', 'Packing Box'] as $proses) {
            $row = $gpRows[$proses] ?? null;
            $durasiBase = (float) ($row->durasi_dtk ?? 0);
            $durasi = $proses === 'Packing Box'
                ? ($durasiBase * ($beratPlusSpare / 5))
                : ($durasiBase * $panjangPlusSpare);

            $mesinCost = $durasi * (float) ($row->biaya_per_detik ?? 0);
            $mpCost = $durasi * $ratePerDetikMp;

            $gpCosts[$proses] = [
                'proses' => $proses,
                'total_durasi_dtk' => $durasi,
                'mesin_cost' => $mesinCost,
                'mp_cost' => $mpCost,
                'total_cost' => $mesinCost + $mpCost,
            ];
        }

        $materialAgg = [];
        foreach ($rawMaterials as $row) {
            $mat = trim((string) $row['material']);
            if ($mat === '' || $mat === '0') {
                continue;
            }
            if (! isset($materialAgg[$mat])) {
                $materialAgg[$mat] = 0;
            }
            $materialAgg[$mat] += $row['berat_kebutuhan_kg'];
        }

        $materialRows = [];
        $materialTotalWeight = 0;

        // Updated from latest calculation sheet.
        $hardcodedPrices = [
            'YP-03NH-NC-GTE-1P' => ['origin_per_kg' => 848300, 'standard_per_kg' => 1737000],
            'YP-07NG-NC-GTE-1P' => ['origin_per_kg' => 848300, 'standard_per_kg' => 1737000],
            'YI-05NG-NC-6YL-1P' => ['origin_per_kg' => 970700, 'standard_per_kg' => 1519000],
            '0' => ['origin_per_kg' => null, 'standard_per_kg' => null],
        ];

        foreach ($materialAgg as $mat => $weight) {
            $normalizedMat = $this->normalizeMaterialCode($mat);
            $price = $hardcodedPrices[$normalizedMat] ?? $hardcodedPrices[$mat] ?? ['origin_per_kg' => null, 'standard_per_kg' => null];
            $originPerKg = $price['origin_per_kg'];
            $standardPerKg = $price['standard_per_kg'];

            $materialRows[] = [
                'material' => $mat,
                'berat_total_kg' => $weight,
                'origin_per_kg' => $originPerKg,
                'standard_per_kg' => $standardPerKg,
                'harga_origin' => $originPerKg ? ($weight * $originPerKg) : 0,
                'harga_standard' => $standardPerKg ? ($weight * $standardPerKg) : 0,
            ];
            $materialTotalWeight += $weight;
        }

        foreach ($materialRows as &$row) {
            $row['percentage'] = $materialTotalWeight > 0 ? ($row['berat_total_kg'] / $materialTotalWeight) : 0;
        }
        unset($row);

        while (count($materialRows) < 4) {
            $materialRows[] = [
                'material' => '0',
                'berat_total_kg' => 0,
                'origin_per_kg' => null,
                'standard_per_kg' => null,
                'harga_origin' => 0,
                'harga_standard' => 0,
                'percentage' => 0,
            ];
        }

        $materialRows = array_slice($materialRows, 0, 4);

        $materialOriginTotal = array_sum(array_column($materialRows, 'harga_origin'));
        $materialStandardTotal = array_sum(array_column($materialRows, 'harga_standard'));

        // Sheet pricing maps by material-row order (Material 1/2/3), not by material prefix.
        $nonZeroMaterialRows = array_values(array_filter(
            $materialRows,
            fn ($row) => ($row['material'] ?? '0') !== '0' && (float) ($row['berat_total_kg'] ?? 0) > 0
        ));

        $slotWeights = [
            'slot1' => (float) ($nonZeroMaterialRows[0]['berat_total_kg'] ?? 0),
            'slot2' => (float) ($nonZeroMaterialRows[1]['berat_total_kg'] ?? 0),
            'slot3' => (float) ($nonZeroMaterialRows[2]['berat_total_kg'] ?? 0),
        ];

        $priceRows = [
            [
                'usd' => 49.9,
                'origin_per_kg' => 848300,
                'standard_per_kg' => 1737000,
                'berat_kg' => $slotWeights['slot1'],
            ],
            [
                'usd' => 57.1,
                'origin_per_kg' => 970700,
                'standard_per_kg' => 1519000,
                'berat_kg' => $slotWeights['slot2'],
            ],
            [
                'usd' => null,
                'origin_per_kg' => null,
                'standard_per_kg' => 1845000,
                'berat_kg' => $slotWeights['slot3'],
            ],
        ];

        foreach ($priceRows as &$priceRow) {
            $berat = (float) ($priceRow['berat_kg'] ?? 0);
            $originPerKg = $priceRow['origin_per_kg'];
            $standardPerKg = $priceRow['standard_per_kg'];
            $priceRow['harga_origin'] = is_null($originPerKg) ? null : ($berat * (float) $originPerKg);
            $priceRow['harga_standard'] = is_null($standardPerKg) ? null : ($berat * (float) $standardPerKg);
        }
        unset($priceRow);

        $materialOriginTotal = array_sum(array_map(
            fn ($row) => (float) ($row['harga_origin'] ?? 0),
            $priceRows
        ));
        $materialStandardTotal = array_sum(array_map(
            fn ($row) => (float) ($row['harga_standard'] ?? 0),
            $priceRows
        ));

        $prosesTotal = $durasiGulungCalc['total_cost']
            + $setupCalc['total_cost']
            + $braidingCalc['total_cost']
            + $gpCosts['Press']['total_cost']
            + $gpCosts['Gulung']['total_cost']
            + $gpCosts['Packing Box']['total_cost'];

        return [
            'input' => $input,
            'kode_barang' => $kodeBarang,
            'density' => $density,
            'spare_panjang_m' => $sparePanjang,
            'spare_berat_kg' => $spareBerat,
            'berat_plus_spare_kg' => $beratPlusSpare,
            'panjang_plus_spare_m' => $panjangPlusSpare,
            'bandul' => [
                'diagonal_sudut' => (float) ($kodeRow->col_l ?? 0),
                'diagonal_tengah' => (float) ($kodeRow->col_m ?? 0),
            ],
            'raw_material' => [
                'rows' => $rawMaterials,
                'total_percent' => $totalRawPercent,
                'total_berat_kg' => $totalRawWeight,
            ],
            'bobbin' => [
                'berat_1_bobbin_full' => $bobbinFullWeight,
                'rows' => $bobbinRows,
            ],
            'durasi_gulung_benang' => [
                'rows' => $durasiRows,
                ...$durasiGulungCalc,
            ],
            'durasi_setup_braiding' => $setupCalc,
            'durasi_braiding_gp' => $braidingCalc,
            'durasi_gp' => [
                'press' => $gpCosts['Press'],
                'gulung' => $gpCosts['Gulung'],
                'packing_box' => $gpCosts['Packing Box'],
            ],
            'harga_material' => [
                'jumlah_material' => count(array_filter($materialRows, fn ($r) => $r['material'] !== '0')),
                'kurs_dollar' => 17000,
                'rows' => $materialRows,
                'price_rows' => $priceRows,
                'total_berat_kg' => $materialTotalWeight,
                'total_harga_origin' => $materialOriginTotal,
                'total_harga_standard' => $materialStandardTotal,
            ],
            'total_harga' => [
                'harga_proses_origin' => $prosesTotal,
                'harga_proses_standard' => $prosesTotal,
                'harga_material_origin' => $materialOriginTotal,
                'harga_material_standard' => $materialStandardTotal,
                'total_origin' => $prosesTotal + $materialOriginTotal,
                'total_standard' => $prosesTotal + $materialStandardTotal,
            ],
        ];
    }

    private function parsePercent(mixed $value): float
    {
        if ($value === null) {
            return 0;
        }

        if (is_numeric($value)) {
            $num = (float) $value;
            return $num > 1 ? $num / 100 : $num;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return 0;
        }

        $str = str_replace('%', '', $str);
        $str = str_replace(',', '.', $str);

        if (! is_numeric($str)) {
            return 0;
        }

        $num = (float) $str;
        return $num > 1 ? $num / 100 : $num;
    }

    private function normalizeMaterialCode(string $material): string
    {
        $material = str_replace("\xc2\xa0", ' ', $material);
        $material = preg_replace('/\s+/', '', strtoupper(trim($material))) ?? '';
        $alnum = preg_replace('/[^A-Z0-9]/', '', $material) ?? '';

        if ($alnum === 'YP03NHNCGTE1P') {
            return 'YP-03NH-NC-GTE-1P';
        }

        if ($alnum === 'YP07NGNCGTE1P') {
            return 'YP-07NG-NC-GTE-1P';
        }

        if ($alnum === 'YI05NGNC6YL1P') {
            return 'YI-05NG-NC-6YL-1P';
        }

        return trim($material);
    }
}
