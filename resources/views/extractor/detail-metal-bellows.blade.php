<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail Metal Bellows</h2>
            <p class="text-sm text-gray-600">Auto tarik dari PCE Item + validasi EJM (strict DB-only).</p>
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
            <form method="GET" action="{{ route('extractor.ejm.detailmetalbellows') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailmetalbellows') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailmetalbellows.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Detail Metal Bellows Dari PCE Item
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Jika rate/data kosong, generate dibatalkan dengan pesan error detail.</span>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[4200px] text-xs">
                    <thead class="bg-slate-200 text-slate-900">
                        <tr>
                            <th class="px-2 py-2 text-left">NO</th>
                            <th class="px-2 py-2 text-left">Size (Inche)</th>
                            <th class="px-2 py-2 text-left">Shape</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">Width</th>
                            <th class="px-2 py-2 text-left">Length</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Ply</th>
                            <th class="px-2 py-2 text-left">OAL</th>
                            <th class="px-2 py-2 text-left">Noc</th>
                            <th class="px-2 py-2 text-left">Material</th>
                            <th class="px-2 py-2 text-left">P / N Bellows</th>
                            <th class="px-2 py-2 text-left">Description Bellows</th>
                            <th class="px-2 py-2 text-left">P / N Collar</th>
                            <th class="px-2 py-2 text-left">Description Collar</th>
                            <th class="px-2 py-2 text-left">Welding Rod</th>
                            <th class="px-2 py-2 text-left">Mesin</th>
                            <th class="px-2 py-2 text-left">Manpower</th>
                            <th class="px-2 py-2 text-left">Grinda Poles</th>
                            <th class="px-2 py-2 text-left">Disc Poles</th>
                            <th class="px-2 py-2 text-left">Harga Bellows</th>
                            <th class="px-2 py-2 text-left">Harga Collar</th>
                            <th class="px-2 py-2 text-left">Rate Welding Rod</th>
                            <th class="px-2 py-2 text-left">Rate Mesin</th>
                            <th class="px-2 py-2 text-left">Rate Manpower</th>
                            <th class="px-2 py-2 text-left">Rate Gerinda Poles</th>
                            <th class="px-2 py-2 text-left">Rate Disc Poles</th>
                            <th class="px-2 py-2 text-left">Total</th>
                            <th class="px-2 py-2 text-left">QTY</th>
                            <th class="px-2 py-2 text-left">Grand Total</th>
                            <th class="px-2 py-2 text-left">P / N Metal Bellows</th>
                            <th class="px-2 py-2 text-left">Description Metal Bellows</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $idx => $item)
                            @php
                                $d = $item->detailMetalBellows;
                                $v = $item->validation;
                                $qty = max(1, (int) ($item->qty ?? 1));

                                $width = $d?->width ?? (($v?->width ?? 0) ?: (($v?->width1 ?? 0) ?: (($item->od_mm ?? 0) ? (pi() * (float) $item->od_mm) : 0)));
                                $length = $d?->length ?? (($v?->length ?? 0) ?: ($v?->can_length ?? $v?->oal_b ?? $v?->oal ?? 0));
                                $oal = $d?->oal ?? $v?->oal;
                                $noc = $d?->noc ?? $item->noc ?? $v?->noc;

                                $hargaBellows = $d?->harga_bellows ?? 0;
                                $hargaCollar = $d?->harga_collar ?? 0;
                                $wrQty = $d?->welding_rod_qty ?? 0;
                                $mesinQty = $d?->mesin_qty ?? 0;
                                $mpQty = $d?->manpower_qty ?? 0;
                                $grQty = $d?->grinda_poles_qty ?? 0;
                                $discQty = $d?->disc_poles_qty ?? 0;

                                $rateWr = $d?->rate_welding_rod ?? ($rates['welding_rod'] ?? 0);
                                $rateMesin = $d?->rate_mesin ?? ($rates['mesin'] ?? 0);
                                $rateMp = $d?->rate_manpower ?? ($rates['manpower'] ?? 0);
                                $rateGr = $d?->rate_grinda_poles ?? ($rates['grinda_poles'] ?? 0);
                                $rateDisc = $d?->rate_disc_poles ?? ($rates['disc_poles'] ?? 0);

                                $total = $d?->total ?? ((float) $hargaBellows + (float) $hargaCollar + ((float) $wrQty * (float) $rateWr) + ((float) $mesinQty * (float) $rateMesin) + ((float) $mpQty * (float) $rateMp) + ((float) $grQty * (float) $rateGr) + ((float) $discQty * (float) $rateDisc));
                                $grandTotal = $d?->grand_total ?? ((float) $total * $qty);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $idx + 1 }}</td>
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $str($item->shape?->shape_name) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($width) }}</td>
                                <td class="px-2 py-2">{{ $fmt($length) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->ply, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($oal) }}</td>
                                <td class="px-2 py-2">{{ $fmt($noc, 0) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->material ?? $item->materialBellow?->material) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number_bellows ?? $item->materialBellow?->part_number) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description_bellows ?? $item->materialBellow?->description ?? $item->materialBellow?->naming) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number_collar) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description_collar) }}</td>
                                <td class="px-2 py-2">{{ $fmt($wrQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($mesinQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($mpQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($grQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($discQty) }}</td>
                                <td class="px-2 py-2">{{ $fmt($hargaBellows) }}</td>
                                <td class="px-2 py-2">{{ $fmt($hargaCollar) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateWr) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateMesin) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateMp) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateGr) }}</td>
                                <td class="px-2 py-2">{{ $fmt($rateDisc) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($total) }}</td>
                                <td class="px-2 py-2">{{ $fmt($qty, 0) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($grandTotal) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->part_number_metal_bellows) }}</td>
                                <td class="px-2 py-2">{{ $str($d?->description_metal_bellows) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="34" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

