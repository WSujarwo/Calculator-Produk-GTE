<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Collar</h2>
            <p class="text-sm text-gray-600">Tarik otomatis dari PCE Item + Validasi EJM + Validasi Proses + Material.</p>
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
            <form method="GET" action="{{ route('extractor.ejm.detailcollar') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailcollar') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailcollar.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Collar Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Strict DB-only (akan error kalau data validasi/rate belum lengkap).</span>
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
                <table class="min-w-[3600px] text-xs">
                    <thead class="bg-cyan-700 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Size (Inch)</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Qty (Kanan-Kiri)</th>
                            <th class="px-2 py-2 text-left">Width</th>
                            <th class="px-2 py-2 text-left">Length</th>
                            <th class="px-2 py-2 text-left">Square (SQM)</th>
                            <th class="px-2 py-2 text-left">Time Cutting (Minute)</th>
                            <th class="px-2 py-2 text-left">Time Roll (Minute)</th>
                            <th class="px-2 py-2 text-left">Time Welding (Minute)</th>
                            <th class="px-2 py-2 text-left">Total Time (Minute)</th>
                            <th class="px-2 py-2 text-left">P / N Plate</th>
                            <th class="px-2 py-2 text-left">Description Plate</th>
                            <th class="px-2 py-2 text-left">P/N Collar</th>
                            <th class="px-2 py-2 text-left">Description Collar</th>
                            <th class="px-2 py-2 text-left">Raw Material</th>
                            <th class="px-2 py-2 text-left">Raw Material Code</th>
                            <th class="px-2 py-2 text-left">Price / sqm</th>
                            <th class="px-2 py-2 text-left">Cost Raw Material</th>
                            <th class="px-2 py-2 text-left">Price Validasi Machine</th>
                            <th class="px-2 py-2 text-left">Cost Machine Material</th>
                            <th class="px-2 py-2 text-left">Rate per Hour</th>
                            <th class="px-2 py-2 text-left">Quantity</th>
                            <th class="px-2 py-2 text-left">Total Cost Manpower</th>
                            <th class="px-2 py-2 text-left">Total Price</th>
                            <th class="px-2 py-2 text-left">Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $d = $item->detailCollar;
                                $v = $item->validation;
                                $p = $processMap[(int) ($item->nb ?? 0)] ?? [];
                                $cut = $p['cutting_shearing'] ?? null;
                                $roll = $p['rolling'] ?? null;
                                $weld = $p['welding'] ?? null;

                                $qtyKananKiri = $d?->qty_kanan_kiri ?? 2;
                                $width = $d?->width ?? (($item->od_mm ?? $v?->od_mm) ? (pi() * (float) ($item->od_mm ?? $v?->od_mm)) : null);
                                $length = $d?->length ?? (float) ($v?->can_length ?? $v?->oal_b ?? $v?->oal ?? 0);
                                $square = $d?->square_sqm ?? (($width && $length) ? (((float) $width * (float) $length) / 1000000) : null);

                                $timeCut = $d?->time_cutting_minute ?? (float) ($cut?->tube_inner ?? 0);
                                $timeRoll = $d?->time_roll_minute ?? (float) ($roll?->tube_inner ?? 0);
                                $timeWeld = $d?->time_welding_minute ?? (float) ($weld?->tube_inner ?? 0);
                                $totalTime = $d?->total_time_minute ?? ((float) $timeCut + (float) $timeRoll + (float) $timeWeld);

                                $pnPlate = $d?->part_number_plate;
                                $descPlate = $d?->description_plate;
                                $pnCollar = $d?->part_number_collar;
                                $descCollar = $d?->description_collar;
                                $rawMaterial = $d?->raw_material ?? $item->materialBellow?->material;
                                $rawCode = $d?->raw_material_code ?? $item->materialBellow?->part_number;
                                $priceSqm = $d?->price_sqm ?? (float) ($item->materialBellow?->price_sqm ?? 0);
                                $spare = (float) ($v?->spare ?? 5);
                                $costRawMat = $d?->cost_raw_material ?? (((float) ($square ?? 0) * (float) $qtyKananKiri * (float) $priceSqm) * (1 + ($spare / 100)));

                                $machineRate = $d?->price_validasi_machine ?? (float) ($rates['machine_minute'] ?? 0);
                                $costMachine = $d?->cost_machine_material ?? ((float) $totalTime * (float) $machineRate);
                                $ratePerHour = $d?->rate_per_hour ?? (float) ($rates['manpower_hour'] ?? 0);
                                $qtyMp = $d?->quantity ?? 2;
                                $costMp = $d?->total_cost_manpower ?? (((float) $totalTime / 60) * (float) $ratePerHour * (float) $qtyMp);
                                $totalPrice = $d?->total_price ?? ((float) $costRawMat + (float) $costMachine + (float) $costMp);
                                $source = $d ? 'DetailCollar' : 'Fallback Runtime';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qtyKananKiri) }}</td>
                                <td class="px-2 py-2">{{ $fmt($width) }}</td>
                                <td class="px-2 py-2">{{ $fmt($length) }}</td>
                                <td class="px-2 py-2">{{ $fmt($square, 4) }}</td>
                                <td class="px-2 py-2">{{ $fmt($timeCut) }}</td>
                                <td class="px-2 py-2">{{ $fmt($timeRoll) }}</td>
                                <td class="px-2 py-2">{{ $fmt($timeWeld) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalTime) }}</td>
                                <td class="px-2 py-2">{{ $str($pnPlate) }}</td>
                                <td class="px-2 py-2">{{ $str($descPlate) }}</td>
                                <td class="px-2 py-2">{{ $str($pnCollar) }}</td>
                                <td class="px-2 py-2">{{ $str($descCollar) }}</td>
                                <td class="px-2 py-2">{{ $str($rawMaterial) }}</td>
                                <td class="px-2 py-2">{{ $str($rawCode) }}</td>
                                <td class="px-2 py-2">{{ $fmt($priceSqm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costRawMat) }}</td>
                                <td class="px-2 py-2">{{ $fmt($machineRate) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costMachine) }}</td>
                                <td class="px-2 py-2">{{ $fmt($ratePerHour) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qtyMp) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costMp) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($totalPrice) }}</td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $d ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $source }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="28" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
