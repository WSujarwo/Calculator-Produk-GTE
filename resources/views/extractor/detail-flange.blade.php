<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Flange</h2>
            <p class="text-sm text-gray-600">Auto tarik dari PCE Item + validasi EJM + validasi material.</p>
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
            <form method="GET" action="{{ route('extractor.ejm.detailflange') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailflange') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailflange.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Flange Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Strict DB-only, rate harus ada di cost_products.</span>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[3600px] text-xs">
                    <thead class="bg-emerald-800 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Size (Inche)</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">Left Material</th>
                            <th class="px-2 py-2 text-left">Left Class</th>
                            <th class="px-2 py-2 text-left">Left Type</th>
                            <th class="px-2 py-2 text-left">Left P/N</th>
                            <th class="px-2 py-2 text-left">Left DESC</th>
                            <th class="px-2 py-2 text-left">Left Qty</th>
                            <th class="px-2 py-2 text-left">Right Material</th>
                            <th class="px-2 py-2 text-left">Right Class</th>
                            <th class="px-2 py-2 text-left">Right Type</th>
                            <th class="px-2 py-2 text-left">Right P/N</th>
                            <th class="px-2 py-2 text-left">Right DESC</th>
                            <th class="px-2 py-2 text-left">Right Qty</th>
                            <th class="px-2 py-2 text-left">Left Flange Price</th>
                            <th class="px-2 py-2 text-left">Left Grinding & Painting</th>
                            <th class="px-2 py-2 text-left">Left Total</th>
                            <th class="px-2 py-2 text-left">Right Flange Price</th>
                            <th class="px-2 py-2 text-left">Right Grinding & Painting</th>
                            <th class="px-2 py-2 text-left">Right Total</th>
                            <th class="px-2 py-2 text-left">Rate per Hour</th>
                            <th class="px-2 py-2 text-left">Quantity</th>
                            <th class="px-2 py-2 text-left">Total Cost</th>
                            <th class="px-2 py-2 text-left">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $d = $item->detailFlange;
                                $v = $item->validation;
                                $mf = $item->materialFlange;

                                $leftFlangePrice = $d?->left_flange_price ?? (float) ($mf?->price_sqm ?? $mf?->price_kg ?? $mf?->price_gram ?? 0);
                                $leftGp = $d?->left_grinding_painting ?? (float) ($rates['grinding_painting'] ?? 0);
                                $leftTotal = $d?->left_total ?? ((float) $leftFlangePrice + (float) $leftGp);

                                $rightFlangePrice = $d?->right_flange_price ?? (float) ($mf?->price_sqm ?? $mf?->price_kg ?? $mf?->price_gram ?? 0);
                                $rightGp = $d?->right_grinding_painting ?? (float) ($rates['grinding_painting'] ?? 0);
                                $rightTotal = $d?->right_total ?? ((float) $rightFlangePrice + (float) $rightGp);

                                $rateHour = $d?->rate_per_hour ?? (float) ($rates['manpower_hour'] ?? 0);
                                $qtyMp = $d?->manpower_qty ?? 2;
                                $totalCost = $d?->total_cost_manpower ?? ((float) $rateHour * (float) $qtyMp);
                                $totalPrice = $d?->total_price ?? ((float) $leftTotal + (float) $rightTotal + (float) $totalCost);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->left_material ?? $mf?->material) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->left_class ?? $mf?->sch) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->left_type ?? $mf?->type) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->left_part_number ?? $mf?->part_number) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->left_description ?? $mf?->description ?? $mf?->naming) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->left_qty ?? 1) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->right_material ?? $mf?->material) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->right_class ?? $mf?->sch) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->right_type ?? $mf?->type) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->right_part_number ?? $mf?->part_number) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->right_description ?? $mf?->description ?? $mf?->naming) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->right_qty ?? 1) }}</td>
                                <td class="px-2 py-2">{{ $fmt($leftFlangePrice) }}</td>
                                <td class="px-2 py-2">{{ $fmt($leftGp) }}</td>
                                <td class="px-2 py-2">{{ $fmt($leftTotal) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rightFlangePrice) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rightGp) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rightTotal) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateHour) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qtyMp) }}</td>
                                <td class="px-2 py-2">{{ $fmt($totalCost) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($totalPrice) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="24" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

