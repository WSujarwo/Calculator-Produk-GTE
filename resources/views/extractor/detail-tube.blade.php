<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Tube</h2>
            <p class="text-sm text-gray-600">Tarik data dari PCE Item (by PCE Header) + relasi validasi EJM.</p>
        </div>
    </x-slot>

    @php
        $fmt = static fn ($value, int $dec = 2) => $value === null || $value === '' ? '-' : number_format((float) $value, $dec, '.', ',');
        $str = static fn ($value) => $value === null || $value === '' ? '-' : (string) $value;
    @endphp

    <div class="w-full px-6 lg:px-10 py-8 space-y-4">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('extractor.ejm.detailtube') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
                <select name="header_id" class="rounded-lg border-slate-300 text-sm">
                    <option value="">-- Pilih PCE Header --</option>
                    @foreach ($recentHeaders as $header)
                        <option value="{{ $header->id }}" @selected((int) request('header_id') === (int) $header->id)>
                            {{ $header->pce_number }} {{ $header->project_name ? '- ' . $header->project_name : '' }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="pce_number" value="{{ $pceNumber }}" placeholder="Atau cari Nomor PCE" class="rounded-lg border-slate-300 text-sm">
                <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Load</button>
                <a href="{{ route('extractor.ejm.detailtube') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>
            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailtube.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Tube Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Upsert ke tabel <code>ejm_detail_tubes</code>, tampilan tetap read-only.</span>
                </form>
            @endif

            <div class="mt-3 text-sm text-slate-600">
                <span class="font-semibold">Header:</span> {{ $selectedHeader?->pce_number ?? '-' }}
                <span class="mx-2">|</span>
                <span class="font-semibold">Project:</span> {{ $selectedHeader?->project_name ?? '-' }}
                <span class="mx-2">|</span>
                <span class="font-semibold">Items:</span> {{ $items->count() }}
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[4200px] text-xs">
                    <thead class="bg-yellow-300 text-slate-900">
                        <tr>
                            <th class="px-2 py-2 text-left">NO</th>
                            <th class="px-2 py-2 text-left">Nama Barang</th>
                            <th class="px-2 py-2 text-left">Size ( Inche )</th>
                            <th class="px-2 py-2 text-left">Shape</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">Width</th>
                            <th class="px-2 py-2 text-left">Length</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Ply</th>
                            <th class="px-2 py-2 text-left">OAL</th>
                            <th class="px-2 py-2 text-left">Material</th>
                            <th class="px-2 py-2 text-left">P / N Plate</th>
                            <th class="px-2 py-2 text-left">Description Plate</th>
                            <th class="px-2 py-2 text-left">Mesin Roll (Minute)</th>
                            <th class="px-2 py-2 text-left">Seam Welding (Minute)</th>
                            <th class="px-2 py-2 text-left">Welding Machine (Minute)</th>
                            <th class="px-2 py-2 text-left">Welding Rod (Minute)</th>
                            <th class="px-2 py-2 text-left">Manpower</th>
                            <th class="px-2 py-2 text-left">Penetrant</th>
                            <th class="px-2 py-2 text-left">Rate Mesin Roll</th>
                            <th class="px-2 py-2 text-left">Rate Seam Welding</th>
                            <th class="px-2 py-2 text-left">Rate Welding Machine</th>
                            <th class="px-2 py-2 text-left">Rate Welding Rod</th>
                            <th class="px-2 py-2 text-left">Harga Material</th>
                            <th class="px-2 py-2 text-left">Total</th>
                            <th class="px-2 py-2 text-left">QTY</th>
                            <th class="px-2 py-2 text-left">Grand Total</th>
                            <th class="px-2 py-2 text-left">P / N</th>
                            <th class="px-2 py-2 text-left">Description</th>
                            <th class="px-2 py-2 text-left">Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $idx => $item)
                            @php
                                $v = $item->validation;
                                $d = $item->detailTube;
                                $p = $processMap[(int) ($item->nb ?? 0)] ?? [];

                                $roll = $p['rolling'] ?? null;
                                $seam = $p['seam_welding'] ?? null;
                                $weldMachine = $p['welding'] ?? null;

                                $mesinRollMinute = $d?->mesin_roll_minute ?? $roll?->tube_inner;
                                $seamWeldingMinute = $d?->seam_welding_minute ?? $seam?->tube_inner;
                                $weldingMachineMinute = $d?->welding_machine_minute ?? $weldMachine?->tube_inner;
                                $weldingRodMinute = $d?->welding_rod_minute;

                                $rateMesinRoll = $d?->rate_mesin_roll ?? $roll?->price_tube_inner;
                                $rateSeamWelding = $d?->rate_seam_welding ?? $seam?->price_tube_inner;
                                $rateWeldingMachine = $d?->rate_welding_machine ?? $weldMachine?->price_tube_inner;
                                $rateWeldingRod = $d?->rate_welding_rod;

                                $nocDynamic = is_numeric($item->noc) ? (float) $item->noc : (float) ($v?->noc ?? 0);
                                $pitch = (float) ($v?->p ?? 0);
                                $lc = (float) ($v?->lc ?? 0);
                                $lpe = (float) ($v?->lpe ?? 0);
                                $gpf = (float) ($v?->gpf ?? 0);
                                $blCalc = $nocDynamic * $pitch;
                                $oalBCalc = (2 * $lc) + $blCalc;
                                $oalCalc = $oalBCalc + (2 * $lpe) + (2 * $lc) + (2 * $gpf);

                                $mat = $item->materialBellow ?? $item->materialPipeEnd;
                                $widthMm = (float) ($v?->width1 ?? 0);
                                if ($widthMm <= 0) {
                                    $diameter = (float) ($item->od_mm ?? $v?->od_mm ?? 0);
                                    $widthMm = $diameter > 0 ? (pi() * $diameter) : 0.0;
                                }
                                $ply = max(1, (int) ($item->ply ?? 1));
                                $thkMm = max(0.1, (float) ($item->thk_mm ?? $mat?->thk_mm ?? 0.5));
                                $areaM2 = $oalBCalc > 0 && $widthMm > 0 ? (($oalBCalc / 1000) * ($widthMm / 1000) * $ply) : 0;
                                $volumeM3 = $areaM2 * ($thkMm / 1000);
                                $weightKg = $volumeM3 * 7850;
                                $weightGr = $weightKg * 1000;

                                $defaultHargaMaterial = 0;
                                if ($mat) {
                                    if ($mat->price_gram !== null) $defaultHargaMaterial = $weightGr * (float) $mat->price_gram;
                                    elseif ($mat->price_kg !== null) $defaultHargaMaterial = $weightKg * (float) $mat->price_kg;
                                    elseif ($mat->price_sqm !== null) $defaultHargaMaterial = $areaM2 * (float) $mat->price_sqm;
                                }

                                $hargaMaterial = $d?->harga_material ?? $defaultHargaMaterial;
                                $manpower = $d?->manpower ?? 2;

                                $biayaProses =
                                    ((float) ($mesinRollMinute ?? 0) * (float) ($rateMesinRoll ?? 0)) +
                                    ((float) ($seamWeldingMinute ?? 0) * (float) ($rateSeamWelding ?? 0)) +
                                    ((float) ($weldingMachineMinute ?? 0) * (float) ($rateWeldingMachine ?? 0)) +
                                    ((float) ($weldingRodMinute ?? 0) * (float) ($rateWeldingRod ?? 0));

                                $total = $d?->total ?? ((float) $hargaMaterial + $biayaProses);
                                $qty = (int) ($item->qty ?? 1);
                                $grandTotal = $total * $qty;

                                $plateCode = 'SS4X';
                                $matName = strtoupper((string) ($item->materialBellow?->material ?? $item->materialPipeEnd?->material ?? ''));
                                if (str_contains($matName, '316')) $plateCode = 'SS6X';
                                elseif (str_contains($matName, '304')) $plateCode = 'SS5X';
                                $fallbackPnPlate = 'BAR-FLAT-' . $plateCode . '-00.' . str_pad((string) ($item->id ?? 0), 3, '0', STR_PAD_LEFT);
                                $fallbackDescPlate = 'BAR-FLAT-' . ($item->materialBellow?->material ?? $item->materialPipeEnd?->material ?? '-') . ', Size : Thk ' . rtrim(rtrim(number_format((float) ($item->thk_mm ?? 0), 3, '.', ''), '0'), '.');

                                $srcProcess = ($d && (
                                    $d->mesin_roll_minute !== null ||
                                    $d->seam_welding_minute !== null ||
                                    $d->welding_machine_minute !== null ||
                                    $d->rate_mesin_roll !== null ||
                                    $d->rate_seam_welding !== null ||
                                    $d->rate_welding_machine !== null
                                )) ? 'DetailTube' : 'Fallback Proses(NB)';

                                $srcMaterial = ($d && $d->harga_material !== null) ? 'DetailTube' : 'Fallback Material';
                                $srcPlate = ($d && ($d->part_number_plate || $d->description_plate)) ? 'DetailTube' : 'Fallback BAR-FLAT';
                                $srcTotal = ($d && $d->total !== null) ? 'DetailTube' : 'Calculated Runtime';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $idx + 1 }}</td>
                                <td class="px-2 py-2">{{ $str($d?->nama_barang ?? $item->description ?? $item->typeConfig?->type_name) }}</td>
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $str($item->shape?->shape_name) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($v?->width) }}</td>
                                <td class="px-2 py-2">{{ $fmt($v?->length) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->ply, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($oalCalc > 0 ? $oalCalc : $v?->oal) }}</td>
                                <td class="px-2 py-2">{{ $str($item->materialBellow?->material) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number_plate ?? $fallbackPnPlate) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description_plate ?? $fallbackDescPlate) }}</td>
                                <td class="px-2 py-2">{{ $fmt($mesinRollMinute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($seamWeldingMinute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($weldingMachineMinute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($weldingRodMinute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($manpower) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->penetrant) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateMesinRoll) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateSeamWelding) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateWeldingMachine) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateWeldingRod) }}</td>
                                <td class="px-2 py-2">{{ $fmt($hargaMaterial) }}</td>
                                <td class="px-2 py-2">{{ $fmt($total) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qty, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($grandTotal) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number ?? $item->materialBellow?->part_number) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description ?? $item->description) }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ str_starts_with($srcProcess, 'DetailTube') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            P: {{ $srcProcess }}
                                        </span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ str_starts_with($srcMaterial, 'DetailTube') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            M: {{ $srcMaterial }}
                                        </span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ str_starts_with($srcPlate, 'DetailTube') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            PL: {{ $srcPlate }}
                                        </span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ str_starts_with($srcTotal, 'DetailTube') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            T: {{ $srcTotal }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="32" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
