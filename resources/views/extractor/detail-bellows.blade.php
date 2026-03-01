<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Bellows</h2>
            <p class="text-sm text-gray-600">Tarik data otomatis dari PCE Item + Validasi EJM + Material + Proses.</p>
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
            <form method="GET" action="{{ route('extractor.ejm.detailbellows') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailbellows') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailbellows.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Bellows Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Upsert ke tabel <code>ejm_detail_bellows</code>, layar tetap read-only.</span>
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
                    <thead class="bg-emerald-800 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Size (Inch)</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Ply</th>
                            <th class="px-2 py-2 text-left">Tube Inner Width</th>
                            <th class="px-2 py-2 text-left">Tube Outer Width</th>
                            <th class="px-2 py-2 text-left">Tube Inner Length</th>
                            <th class="px-2 py-2 text-left">Tube Outer Length</th>
                            <th class="px-2 py-2 text-left">Tube Inner SQM</th>
                            <th class="px-2 py-2 text-left">Tube Outer SQM</th>
                            <th class="px-2 py-2 text-left">Time Cutting I</th>
                            <th class="px-2 py-2 text-left">Time Cutting O</th>
                            <th class="px-2 py-2 text-left">Time Roll I</th>
                            <th class="px-2 py-2 text-left">Time Roll O</th>
                            <th class="px-2 py-2 text-left">Time Welding I</th>
                            <th class="px-2 py-2 text-left">Time Welding O</th>
                            <th class="px-2 py-2 text-left">Time Hydro I</th>
                            <th class="px-2 py-2 text-left">Time Hydro O</th>
                            <th class="px-2 py-2 text-left">Total Time (Min)</th>
                            <th class="px-2 py-2 text-left">P / N Plate</th>
                            <th class="px-2 py-2 text-left">Description Plate</th>
                            <th class="px-2 py-2 text-left">P / N Tube</th>
                            <th class="px-2 py-2 text-left">Description Tube</th>
                            <th class="px-2 py-2 text-left">P / N Bellows</th>
                            <th class="px-2 py-2 text-left">Description</th>
                            <th class="px-2 py-2 text-left">Raw Material</th>
                            <th class="px-2 py-2 text-left">Raw Material Code</th>
                            <th class="px-2 py-2 text-left">Price/SQM</th>
                            <th class="px-2 py-2 text-left">Cost Raw Material</th>
                            <th class="px-2 py-2 text-left">Machine Rate/Min</th>
                            <th class="px-2 py-2 text-left">Machine Cost</th>
                            <th class="px-2 py-2 text-left">Total Cost Raw</th>
                            <th class="px-2 py-2 text-left">Partner Hour</th>
                            <th class="px-2 py-2 text-left">Manpower Qty</th>
                            <th class="px-2 py-2 text-left">Total Cost Manpower</th>
                            <th class="px-2 py-2 text-left">Total Price</th>
                            <th class="px-2 py-2 text-left">Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $d = $item->detailBellows;
                                $v = $item->validation;
                                $p = $processMap[(int) ($item->nb ?? 0)] ?? [];
                                $cut = $p['cutting_shearing'] ?? null;
                                $roll = $p['rolling'] ?? null;
                                $seam = $p['seam_welding'] ?? null;
                                $hydro = $p['hydro_forming'] ?? null;

                                $widthInner = $d?->width_inner ?? (($item->id_mm ?? $v?->id_mm) ? (pi() * (float) ($item->id_mm ?? $v?->id_mm)) : null);
                                $widthOuter = $d?->width_outer ?? (($item->od_mm ?? $v?->od_mm) ? (pi() * (float) ($item->od_mm ?? $v?->od_mm)) : null);
                                $lengthInner = $d?->length_inner ?? (float) ($v?->can_length ?? $v?->oal_b ?? $v?->oal ?? 0);
                                $lengthOuter = $d?->length_outer ?? (float) ($v?->can_length ?? $v?->oal_b ?? $v?->oal ?? 0);

                                $squareInner = $d?->square_inner_sqm ?? (($widthInner && $lengthInner) ? (((float) $widthInner * (float) $lengthInner) / 1000000) : null);
                                $squareOuter = $d?->square_outer_sqm ?? (($widthOuter && $lengthOuter) ? (((float) $widthOuter * (float) $lengthOuter) / 1000000) : null);

                                $tCutI = $d?->time_cutting_inner ?? (float) ($cut?->tube_inner ?? 0);
                                $tCutO = $d?->time_cutting_outer ?? (float) ($cut?->tube_outer ?? 0);
                                $tRollI = $d?->time_roll_inner ?? (float) ($roll?->tube_inner ?? 0);
                                $tRollO = $d?->time_roll_outer ?? (float) ($roll?->tube_outer ?? 0);
                                $tWeldI = $d?->time_welding_inner ?? (float) ($seam?->tube_inner ?? 0);
                                $tWeldO = $d?->time_welding_outer ?? (float) ($seam?->tube_outer ?? 0);
                                $tHydroI = $d?->time_hydroforming_inner ?? (float) ($hydro?->tube_inner ?? 0);
                                $tHydroO = $d?->time_hydroforming_outer ?? (float) ($hydro?->tube_outer ?? 0);

                                $fallbackTotalTime = ($tCutI + $tRollI + $tWeldI) + ($tCutO + $tRollO + $tWeldO) + max($tHydroI, $tHydroO);
                                $totalTime = $d?->total_time_minute ?? $fallbackTotalTime;

                                $rawMaterial = $d?->raw_material ?? $item->materialBellow?->material;
                                $rawCode = $d?->raw_material_code ?? $item->materialBellow?->part_number;
                                $pnPlate = $d?->part_number_plate;
                                $descPlate = $d?->description_plate;
                                $pnTube = $d?->part_number_tube ?? $item->materialPipeEnd?->part_number;
                                $descTube = $d?->description_tube ?? $item->materialPipeEnd?->description ?? $item->materialPipeEnd?->naming;
                                $pnBellows = $d?->part_number_bellows ?? $item->materialBellow?->part_number;
                                $descBellows = $d?->description_bellows ?? $item->description ?? $item->typeConfig?->type_name;
                                $priceSqm = $d?->raw_material_price_sqm ?? (float) ($item->materialBellow?->price_sqm ?? 0);

                                $ply = max(1, (int) ($item->ply ?? 1));
                                $spare = (float) ($v?->spare ?? 5);
                                $fallbackCostRawMat = (((float) ($squareInner ?? 0) + (float) ($squareOuter ?? 0)) * $ply) * (float) $priceSqm * (1 + ($spare / 100));
                                $costRawMat = $d?->cost_raw_material ?? $fallbackCostRawMat;

                                $machineRate = $d?->machine_rate_per_minute ?? (float) ($rates['machine_minute'] ?? 1000);
                                $machineCost = $d?->machine_cost ?? ((float) $totalTime * (float) $machineRate);
                                $totalCostRaw = $d?->total_cost_raw ?? ((float) $costRawMat + (float) $machineCost);

                                $partnerHour = $d?->partner_hour_rate ?? (float) ($rates['manpower_hour'] ?? 52500);
                                $manpowerQty = $d?->manpower_qty ?? 2;
                                $totalCostManpower = $d?->total_cost_manpower ?? (((float) $totalTime / 60) * (float) $partnerHour * (float) $manpowerQty);

                                $totalPrice = $d?->total_price ?? ((float) $totalCostRaw + (float) $totalCostManpower);
                                $source = $d ? 'DetailBellows' : 'Fallback Runtime';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->ply, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($widthInner) }}</td>
                                <td class="px-2 py-2">{{ $fmt($widthOuter) }}</td>
                                <td class="px-2 py-2">{{ $fmt($lengthInner) }}</td>
                                <td class="px-2 py-2">{{ $fmt($lengthOuter) }}</td>
                                <td class="px-2 py-2">{{ $fmt($squareInner, 4) }}</td>
                                <td class="px-2 py-2">{{ $fmt($squareOuter, 4) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tCutI) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tCutO) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tRollI) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tRollO) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tWeldI) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tWeldO) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tHydroI) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tHydroO) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalTime) }}</td>
                                <td class="px-2 py-2">{{ $str($pnPlate) }}</td>
                                <td class="px-2 py-2">{{ $str($descPlate) }}</td>
                                <td class="px-2 py-2">{{ $str($pnTube) }}</td>
                                <td class="px-2 py-2">{{ $str($descTube) }}</td>
                                <td class="px-2 py-2">{{ $str($pnBellows) }}</td>
                                <td class="px-2 py-2">{{ $str($descBellows) }}</td>
                                <td class="px-2 py-2">{{ $str($rawMaterial) }}</td>
                                <td class="px-2 py-2">{{ $str($rawCode) }}</td>
                                <td class="px-2 py-2">{{ $fmt($priceSqm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costRawMat) }}</td>
                                <td class="px-2 py-2">{{ $fmt($machineRate) }}</td>
                                <td class="px-2 py-2">{{ $fmt($machineCost) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalCostRaw) }}</td>
                                <td class="px-2 py-2">{{ $fmt($partnerHour) }}</td>
                                <td class="px-2 py-2">{{ $fmt($manpowerQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalCostManpower) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($totalPrice) }}</td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $d ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $source }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="39" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
