<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Pipe End</h2>
            <p class="text-sm text-gray-600">Auto tarik dari PCE Item + validasi EJM + validasi proses.</p>
        </div>
    </x-slot>

    @php
        $fmt = static fn ($value, int $dec = 2) => $value === null || $value === '' ? '-' : number_format((float) $value, $dec, '.', ',');
        $str = static fn ($value) => $value === null || $value === '' ? '-' : (string) $value;
    @endphp

    <div class="w-full px-6 lg:px-10 py-8 space-y-4">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('extractor.ejm.detailpipeend') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailpipeend') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailpipeend.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Pipe End Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Strict DB-only, error detail muncul kalau data/rate kosong.</span>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[3400px] text-xs">
                    <thead class="bg-emerald-800 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Size (Inche)</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Qty</th>
                            <th class="px-2 py-2 text-left">Length</th>
                            <th class="px-2 py-2 text-left">Time Cutting (Minute)</th>
                            <th class="px-2 py-2 text-left">Time Bevel (Minute)</th>
                            <th class="px-2 py-2 text-left">Time Grinding (Minute)</th>
                            <th class="px-2 py-2 text-left">Total Time (Minute)</th>
                            <th class="px-2 py-2 text-left">Raw Material</th>
                            <th class="px-2 py-2 text-left">Raw Material Code</th>
                            <th class="px-2 py-2 text-left">Price / sqm</th>
                            <th class="px-2 py-2 text-left">Cost Raw Material</th>
                            <th class="px-2 py-2 text-left">Price Validasi Machine</th>
                            <th class="px-2 py-2 text-left">Cost Machine</th>
                            <th class="px-2 py-2 text-left">Rate per Hour</th>
                            <th class="px-2 py-2 text-left">Quantity</th>
                            <th class="px-2 py-2 text-left">Total Cost</th>
                            <th class="px-2 py-2 text-left">Total Price</th>
                            <th class="px-2 py-2 text-left">P / N Pipe End</th>
                            <th class="px-2 py-2 text-left">Description Pipe End</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $d = $item->detailPipeEnd;
                                $v = $item->validation;
                                $p = $processMap[(int) ($item->nb ?? 0)] ?? [];
                                $cut = $p['cutting'] ?? null;
                                $bevel = $p['bevel'] ?? null;
                                $grinding = $p['grinding'] ?? null;

                                $length = $d?->length ?? (float) ($v?->can_length ?? $v?->oal_b ?? $v?->oal ?? 0);
                                $tCut = $d?->time_cutting_minute ?? (float) ($cut?->tube_inner ?? 0);
                                $tBevel = $d?->time_bevel_minute ?? (float) ($bevel?->tube_inner ?? 0);
                                $tGrinding = $d?->time_grinding_minute ?? (float) ($grinding?->tube_inner ?? 0);
                                $totalTime = $d?->total_time_minute ?? ((float) $tCut + (float) $tBevel + (float) $tGrinding);

                                $rawMaterial = $d?->raw_material ?? $item->materialPipeEnd?->material;
                                $rawCode = $d?->raw_material_code ?? $item->materialPipeEnd?->part_number;
                                $priceSqm = $d?->price_sqm ?? (float) ($item->materialPipeEnd?->price_sqm ?? 0);

                                $od = (float) ($item->od_mm ?? $v?->od_mm ?? 0);
                                $width = $od > 0 ? (pi() * $od) : 0;
                                $square = ($width > 0 && $length > 0) ? (($width * $length) / 1000000) : 0;
                                $costRaw = $d?->cost_raw_material ?? ($square * (float) $priceSqm);

                                $priceMachine = $d?->price_validasi_machine ?? (float) ($rates['machine_minute'] ?? 0);
                                $costMachine = $d?->cost_machine ?? ((float) $totalTime * (float) $priceMachine);
                                $rateHour = $d?->rate_per_hour ?? (float) ($rates['manpower_hour'] ?? 0);
                                $qtyManpower = $d?->quantity ?? 2;
                                $totalCost = $d?->total_cost ?? (((float) $totalTime / 60) * (float) $rateHour * (float) $qtyManpower);
                                $qtyItem = max(1, (int) ($item->qty ?? 1));
                                $totalPrice = $d?->total_price ?? (((float) $costRaw + (float) $costMachine + (float) $totalCost) * $qtyItem);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qtyItem, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($length) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tCut) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tBevel) }}</td>
                                <td class="px-2 py-2">{{ $fmt($tGrinding) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalTime) }}</td>
                                <td class="px-2 py-2">{{ $str($rawMaterial) }}</td>
                                <td class="px-2 py-2">{{ $str($rawCode) }}</td>
                                <td class="px-2 py-2">{{ $fmt($priceSqm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costRaw) }}</td>
                                <td class="px-2 py-2">{{ $fmt($priceMachine) }}</td>
                                <td class="px-2 py-2">{{ $fmt($costMachine) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateHour) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qtyManpower) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalCost) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($totalPrice) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number_pipe_end ?? $item->materialPipeEnd?->part_number) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description_pipe_end ?? $item->materialPipeEnd?->description ?? $item->materialPipeEnd?->naming) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="23" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

