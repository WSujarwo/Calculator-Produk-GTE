<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Extractor EJM - Detail EJM (Final)</h2>
            <p class="text-sm text-gray-600">Tarik dari detail Bellows, Collar, Metal Bellows, Pipe End, Flange + proses EJM Production.</p>
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
        @if ($errors->any())
            <div class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('extractor.ejm.detailejm') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-[240px_1fr_auto_auto]">
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
                <a href="{{ route('extractor.ejm.detailejm') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 text-center">Reset</a>
            </form>

            @if ($selectedHeader)
                <form method="POST" action="{{ route('extractor.ejm.detailejm.generate') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="header_id" value="{{ $selectedHeader->id }}">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Generate Final Detail EJM
                    </button>
                    <span class="ml-2 text-xs text-slate-500">Strict DB-only. Jika detail/rate/proses kosong, generate dibatalkan.</span>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[3200px] text-xs">
                    <thead class="bg-emerald-800 text-white">
                        <tr>
                            <th class="px-2 py-2 text-left">Size (Inche)</th>
                            <th class="px-2 py-2 text-left">NB</th>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">OD</th>
                            <th class="px-2 py-2 text-left">Thk</th>
                            <th class="px-2 py-2 text-left">Ply</th>

                            <th class="px-2 py-2 text-left">Material Bellows</th>
                            <th class="px-2 py-2 text-left">Material Pipe End</th>
                            <th class="px-2 py-2 text-left">Material Flange</th>

                            <th class="px-2 py-2 text-left">Assembly (Minute)</th>
                            <th class="px-2 py-2 text-left">Painting (Minute)</th>
                            <th class="px-2 py-2 text-left">Finishing (Minute)</th>
                            <th class="px-2 py-2 text-left">Manpower Rate/Hr</th>
                            <th class="px-2 py-2 text-left">Total Time (Hour)</th>
                            <th class="px-2 py-2 text-left">Manpower Cost</th>

                            <th class="px-2 py-2 text-left">Bellows</th>
                            <th class="px-2 py-2 text-left">Collar</th>
                            <th class="px-2 py-2 text-left">Metal Bellows</th>
                            <th class="px-2 py-2 text-left">Pipe End</th>
                            <th class="px-2 py-2 text-left">Flange</th>
                            <th class="px-2 py-2 text-left">Total</th>

                            <th class="px-2 py-2 text-left">Margin (%)</th>
                            <th class="px-2 py-2 text-left">Margin Amount</th>
                            <th class="px-2 py-2 text-left">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $v = $item->validation;
                                $d = $item->detailEjm;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-2 py-2">{{ $fmt($v?->inch, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->nb, 0) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->id_mm, 2) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->od_mm, 2) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->thk_mm, 3) }}</td>
                                <td class="px-2 py-2">{{ $fmt($item->ply, 0) }}</td>

                                <td class="px-2 py-2">{{ $fmt($d?->material_bellows) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->material_pipe_end) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->material_flange) }}</td>

                                <td class="px-2 py-2">{{ $fmt($d?->time_assembly_minute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->time_painting_minute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->time_finishing_minute) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->manpower_rate_per_hour) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->total_time_hour, 4) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->manpower_cost) }}</td>

                                <td class="px-2 py-2">{{ $fmt($d?->total_bellows) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->total_collar) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->total_metal_bellows) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->total_pipe_end) }}</td>
                                <td class="px-2 py-2">{{ $fmt($d?->total_flange) }}</td>
                                <td class="px-2 py-2 font-semibold">{{ $fmt($d?->total) }}</td>

                                <td class="px-2 py-2">
                                    @if ($d)
                                        <form method="POST" action="{{ route('extractor.ejm.detailejm.margin') }}" class="flex items-center gap-1">
                                            @csrf
                                            <input type="hidden" name="row_id" value="{{ $d->id }}">
                                            <input type="hidden" name="header_id" value="{{ $selectedHeader?->id }}">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="1000"
                                                name="margin_percent"
                                                value="{{ old('margin_percent', $d->margin_percent) }}"
                                                class="w-20 rounded-md border-slate-300 text-xs"
                                            >
                                            <button type="submit" class="rounded bg-slate-800 px-2 py-1 text-[10px] font-semibold text-white hover:bg-slate-900">
                                                Save
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">{{ $fmt($d?->margin_amount) }}</td>
                                <td class="px-2 py-2 font-semibold text-emerald-700">{{ $fmt($d?->grand_total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="24" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

