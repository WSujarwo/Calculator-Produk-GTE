<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Calculation GPP</h2>
            <p class="text-sm text-gray-300">Dry Braided Gland Packing Calculation</p>
        </div>
    </x-slot>

    @php
        $f = fn($v, $d = 2) => is_null($v) ? '-' : number_format((float) $v, $d, '.', ',');
        $calc = $calc ?? null;
        $activeInput = $activeInput ?? [
            'quotation_id' => null,
            'type' => '-- Select Type --',
            'mesin' => $defaultMesin ?? '-- Select --',
            'size' => $defaultSize ?? '-- Select --',
            'berat' => '-- Select --',
            'kelebihan_pengiriman' => '-- Select --',
        ];
        $activeInput = [
            'quotation_id' => old('quotation_id', $activeInput['quotation_id'] ?? null),
            'type' => old('type', $activeInput['type'] ?? ($defaultType ?? '-- Select --')),
            'mesin' => old('mesin', $activeInput['mesin'] ?? ($defaultMesin ?? '-- Select --')),
            'size' => old('size', $activeInput['size'] ?? ($defaultSize ?? '-- Select --')),
            'berat' => old('berat', $activeInput['berat'] ?? '-- Select --'),
            'kelebihan_pengiriman' => old('kelebihan_pengiriman', $activeInput['kelebihan_pengiriman'] ??'-- Select --'),
        ];
        $areaOrder = ['diagonal_sudut', 'diagonal_tengah', 'corner', 'core', 'inner', 'core_cord'];
        $gpCardMap = [
            'Durasi Proses Press Gland Packing' => ['key' => 'press', 'label' => 'Press'],
            'Durasi Proses Gulung Gland Packing' => ['key' => 'gulung', 'label' => 'Gulung'],
            'Durasi Proses Packing Gland Packing' => ['key' => 'packing_box', 'label' => 'Packing Box'],
        ];
    @endphp

    <div class="w-full px-6 lg:px-10 py-6 flex flex-col gap-6">
        <div id="gpp-main-calculation-card" class="hidden rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-bold text-white">
                Dry Braided Gland Packing Calculation
            </div>
            <div class="bg-slate-50 border-b border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900">
                Type dan Size
            </div>

            <form id="gpp-main-form" class="p-4 lg:p-5 space-y-4" method="POST" action="{{ route('calculation.gpp.validate') }}">
                @csrf
                <input type="hidden" id="gpp-quotation-id" name="quotation_id" value="{{ $activeInput['quotation_id'] ?? '' }}">

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="w-1/2 bg-indigo-50 px-3 py-2 font-semibold">
                                        Pilih Type
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <select id="gpp-type" name="type" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Type --</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type }}" @selected(($activeInput['type'] ?? $defaultType) === $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-indigo-50 px-3 py-2 font-semibold">
                                        Pilih Mesin
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <select id="gpp-mesin" name="mesin" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach ($mesins as $mesin)
                                                <option value="{{ $mesin }}" @selected(($activeInput['mesin'] ?? $defaultMesin) === $mesin)>{{ $mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-indigo-50 px-3 py-2 font-semibold">
                                        Pilih Size
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <select id="gpp-size" name="size" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Size --</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size }}" @selected(($activeInput['size'] ?? $defaultSize) === $size)>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Kode Barang</td>
                                    <td class="bg-white px-3 py-2 text-gray-700">{{ $calc['kode_barang'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Density</td>
                                    <td class="bg-white px-3 py-2 text-gray-700">{{ isset($calc) ? $f($calc['density'], 4) : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="w-1/2 bg-indigo-50 px-3 py-2 font-semibold">
                                        Input Berat (kg)
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="berat" value="{{ $activeInput['berat'] ?? 100 }}" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-indigo-50 px-3 py-2 font-semibold">
                                        *Input Kelebihan Saat Pengiriman (kg)
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="kelebihan_pengiriman" value="{{ $activeInput['kelebihan_pengiriman'] ?? 5 }}" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Spare Panjang (m)</td>
                                    <td class="bg-white px-3 py-2">{{ isset($calc) ? $f($calc['spare_panjang_m'], 4) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Spare Berat (kg)</td>
                                    <td class="bg-white px-3 py-2">{{ isset($calc) ? $f($calc['spare_berat_kg'], 4) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Berat + Spare (kg)</td>
                                    <td class="bg-white px-3 py-2 font-semibold">{{ isset($calc) ? $f($calc['berat_plus_spare_kg'], 4) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-slate-100 px-3 py-2 font-semibold">Panjang + Spare (m)</td>
                                    <td class="bg-white px-3 py-2">{{ isset($calc) ? $f($calc['panjang_plus_spare_m'], 4) : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <div class="bg-slate-700 px-3 py-2 text-sm font-semibold text-white">Bandul</div>
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Bandul</th>
                                    <th class="px-3 py-2 text-right">Berat (kg)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-3 py-2">Diagonal Sudut</td>
                                    <td class="px-3 py-2 text-right">{{ isset($calc) ? $f($calc['bandul']['diagonal_sudut'], 4) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2">Diagonal Tengah</td>
                                    <td class="px-3 py-2 text-right">{{ isset($calc) ? $f($calc['bandul']['diagonal_tengah'], 4) : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>

        <div class="order-first rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Calculation GlandPacking
            </div>
            <div class="p-4 space-y-4">
                @if (session('status'))
                    <div class="rounded-lg border border-emerald-800 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-lg border border-red-800 bg-red-950/40 px-4 py-3 text-sm text-red-300">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800">Quotation Aktif</label>
                        <div class="mt-2 rounded-md border border-indigo-300 bg-white px-3 py-2 text-sm text-slate-800">
                            {{ $selectedQuotation->quotation_no ?? 'Belum ada quotation. Silakan Create Quotation terlebih dahulu.' }}
                        </div>
                    </div>
                    @if ($selectedQuotation)
                        <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-medium text-slate-500 cursor-not-allowed" disabled>
                            Quotation Sudah Dibuat
                        </button>
                    @else
                        <a href="{{ route('settings.quotations.create', ['return_to' => 'gpp']) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100">
                            Create Quotation
                        </a>
                    @endif
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">Input Produk</div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="px-3 py-2 text-slate-600">Type</td>
                                    <td class="px-3 py-2">
                                        <select id="ws-type" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Type --</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type }}" @selected(($activeInput['type'] ?? $defaultType) === $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-slate-600">Mesin</td>
                                    <td class="px-3 py-2">
                                        <select id="ws-mesin" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach ($mesins as $mesin)
                                                <option value="{{ $mesin }}" @selected(($activeInput['mesin'] ?? $defaultMesin) === $mesin)>{{ $mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-slate-600">Size</td>
                                    <td class="px-3 py-2">
                                        <select id="ws-size" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Size --</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size }}" @selected(($activeInput['size'] ?? $defaultSize) === $size)>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-slate-600">Input Berat (kg)</td>
                                    <td class="px-3 py-2">
                                        <input id="ws-berat" type="number" step="0.01" min="0" value="{{ is_numeric($activeInput['berat']) ? $activeInput['berat'] : '' }}" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-slate-600">Input Kelebihan (kg)</td>
                                    <td class="px-3 py-2">
                                        <input id="ws-kelebihan" type="number" step="0.01" min="0" value="{{ is_numeric($activeInput['kelebihan_pengiriman']) ? $activeInput['kelebihan_pengiriman'] : '' }}" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">Point Hasil Kalkulasi</div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-3 py-2 text-slate-600">Kode Barang</td><td class="px-3 py-2 font-medium text-slate-900">{{ $calc['kode_barang'] ?? '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Berat + Spare (kg)</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? $f($calc['berat_plus_spare_kg'], 2) : '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Total Raw Material (kg)</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? $f($calc['raw_material']['total_berat_kg'], 2) : '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Total Harga Origin</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? 'Rp '.$f($calc['total_harga']['total_origin'], 0) : '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Total Harga Standarisasi</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? 'Rp '.$f($calc['total_harga']['total_standard'], 0) : '-' }}</td></tr>
                                <tr>
                                    <td class="px-3 py-2 text-slate-600 align-top">P/N Layer (Qty &amp; Berat)</td>
                                    <td class="px-3 py-2 font-medium text-slate-900">
                                        @if (isset($calc))
                                            @php
                                                $materialQtyMap = collect($calc['raw_material']['rows'] ?? [])
                                                    ->filter(fn ($row) => ($row['material'] ?? '0') !== '0' && (($row['berat_kebutuhan_kg'] ?? 0) > 0))
                                                    ->groupBy('material')
                                                    ->map(fn ($rows) => $rows->count());

                                                $materialRows = collect($calc['harga_material']['rows'] ?? [])
                                                    ->filter(fn ($row) => ($row['material'] ?? '0') !== '0' && (($row['berat_total_kg'] ?? 0) > 0))
                                                    ->values();
                                            @endphp
                                            @if ($materialRows->isEmpty())
                                                -
                                            @else
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead>
                                                            <tr class="text-slate-500">
                                                                <th class="py-1 pr-2 text-left">P/N</th>
                                                                <th class="py-1 pr-2 text-right">Qty</th>
                                                                <th class="py-1 text-right">Berat (kg)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($materialRows as $row)
                                                                <tr>
                                                                    <td class="py-1 pr-2">{{ $row['material'] }}</td>
                                                                    <td class="py-1 pr-2 text-right">{{ $materialQtyMap[$row['material']] ?? 0 }}</td>
                                                                    <td class="py-1 text-right">{{ $f($row['berat_total_kg'], 4) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr><td class="px-3 py-2 text-slate-600">Total Proses Gulung</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? 'Rp '.$f((($calc['durasi_gulung_benang']['total_cost'] ?? 0) + ($calc['durasi_gp']['gulung']['total_cost'] ?? 0)), 0) : '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Total Proses Braiding</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? 'Rp '.$f((($calc['durasi_setup_braiding']['total_cost'] ?? 0) + ($calc['durasi_braiding_gp']['total_cost'] ?? 0)), 0) : '-' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">Daftar Produk Dalam Quotation Terpilih</h4>
                    <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                        <button id="gpp-reset-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Reset Input
                        </button>
                        <button id="add-gpp-item-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50" {{ isset($calc) ? '' : 'disabled' }}>
                            Tambah Produk Ini
                        </button>
                        <button id="clear-gpp-items-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Kosongkan Daftar
                        </button>
                        <button id="toggle-gpp-detail-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Lihat Detail Kalkulasi
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Mesin</th>
                                <th class="px-3 py-2 text-left">Size</th>
                                <th class="px-3 py-2 text-right">Berat</th>
                                <th class="px-3 py-2 text-left">Kode Barang</th>
                                <th class="px-3 py-2 text-right">Total Standarisasi</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="gpp-quotation-items-body" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada item.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="gpp-detail-sections" class="hidden space-y-6">
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Berat dan Persentase Kebutuhan Raw Material</div>
            <div class="p-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Benang</th>
                                <th class="px-3 py-2 text-left">Material</th>
                                <th class="px-3 py-2 text-right">Berat per Meter (kg)</th>
                                <th class="px-3 py-2 text-right">Persentase (%)</th>
                                <th class="px-3 py-2 text-right">Berat Kebutuhan (kg)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($areaOrder as $key)
                                @php $row = $calc['raw_material']['rows'][$key] ?? null; @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $row['area'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['material'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['berat_per_meter_kg'], 6) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f(($row['percent'] ?? 0) * 100, 2) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['berat_kebutuhan_kg'], 2) : '-' }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-100/60 font-semibold">
                                <td class="px-3 py-2">Total Raw Material</td>
                                <td class="px-3 py-2">-</td>
                                <td class="px-3 py-2 text-right">-</td>
                                <td class="px-3 py-2 text-right">{{ isset($calc) ? $f($calc['raw_material']['total_percent'] * 100, 2) : '-' }}</td>
                                <td class="px-3 py-2 text-right">{{ isset($calc) ? $f($calc['raw_material']['total_berat_kg'], 4) : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Proses Gulung Benang 1 Bobbin (1 Diagonal, 1 Corner, 1 Inner dan 1 Core)</div>
            <div class="p-4 space-y-3">
                <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden text-sm">
                    <span class="bg-slate-100 px-3 py-2 font-semibold">Berat 1 bobbin full</span>
                    <span class="bg-slate-100 px-3 py-2 font-semibold">{{ isset($calc) ? $f($calc['bobbin']['berat_1_bobbin_full'], 2) : '0.75' }}</span>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Area</th>
                                <th class="px-3 py-2 text-right">Ply</th>
                                <th class="px-3 py-2 text-right">Bobbin Proses</th>
                                <th class="px-3 py-2 text-right">Berat Kebutuhan (kg)</th>
                                <th class="px-3 py-2 text-right">Bobbin Full</th>
                                <th class="px-3 py-2 text-right">Bobbin != Full</th>
                                <th class="px-3 py-2 text-left">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($areaOrder as $key)
                                @php $row = $calc['bobbin']['rows'][$key] ?? null; @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $row['area'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['ply'], 0) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['bobbin_proses'], 0) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['berat_kebutuhan_kg'], 2) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['bobbin_full'], 0) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['bobbin_not_full'], 2) : '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['description'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Durasi Proses Gulung Benang</div>
            <div class="p-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Area</th>
                                <th class="px-3 py-2 text-right">Total Durasi (dtk)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($areaOrder as $key)
                                @php $row = $calc['durasi_gulung_benang']['rows'][$key] ?? null; @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $row['area'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row ? $f($row['total_durasi_dtk'], 2) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="rounded-xl border border-gray-200 p-4 text-sm">
                    <div class="font-semibold mb-2">Perhitungan dengan Durasi Detik</div>
                    <div class="space-y-2">
                        <div class="flex justify-between"><span>Mesin</span><span>Rp {{ isset($calc) ? $f($calc['durasi_gulung_benang']['mesin_cost'], 0) : '-' }}</span></div>
                        <div class="flex justify-between"><span>MP</span><span>Rp {{ isset($calc) ? $f($calc['durasi_gulung_benang']['mp_cost'], 0) : '-' }}</span></div>
                        <div class="h-px bg-gray-200"></div>
                        <div class="flex justify-between font-semibold"><span>Total</span><span>Rp {{ isset($calc) ? $f($calc['durasi_gulung_benang']['total_cost'], 0) : '-' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
                <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Durasi Proses Set Up Mesin Braiding</div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span>Total Durasi (dtk)</span><span>{{ isset($calc) ? $f($calc['durasi_setup_braiding']['total_durasi_dtk'], 2) : '-' }}</span></div>
                    <div class="flex justify-between"><span>Mesin</span><span>Rp {{ isset($calc) ? $f($calc['durasi_setup_braiding']['mesin_cost'], 0) : '-' }}</span></div>
                    <div class="flex justify-between"><span>MP</span><span>Rp {{ isset($calc) ? $f($calc['durasi_setup_braiding']['mp_cost'], 0) : '-' }}</span></div>
                    <div class="h-px bg-gray-200 my-2"></div>
                    <div class="flex justify-between font-semibold"><span>Total</span><span>Rp {{ isset($calc) ? $f($calc['durasi_setup_braiding']['total_cost'], 0) : '-' }}</span></div>
                </div>
            </div>
            <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
                <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Durasi Proses Braiding Gland Packing</div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span>Total Durasi (dtk)</span><span>{{ isset($calc) ? $f($calc['durasi_braiding_gp']['total_durasi_dtk'], 2) : '-' }}</span></div>
                    <div class="flex justify-between"><span>Mesin</span><span>Rp {{ isset($calc) ? $f($calc['durasi_braiding_gp']['mesin_cost'], 0) : '-' }}</span></div>
                    <div class="flex justify-between"><span>MP</span><span>Rp {{ isset($calc) ? $f($calc['durasi_braiding_gp']['mp_cost'], 0) : '-' }}</span></div>
                    <div class="h-px bg-gray-200 my-2"></div>
                    <div class="flex justify-between font-semibold"><span>Total</span><span>Rp {{ isset($calc) ? $f($calc['durasi_braiding_gp']['total_cost'], 0) : '-' }}</span></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            @foreach ($gpCardMap as $title => $meta)
                @php $row = $calc['durasi_gp'][$meta['key']] ?? null; @endphp
                <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
                    <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">{{ $title }}</div>
                    <div class="p-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span>Proses</span><span class="font-semibold">{{ $meta['label'] }}</span></div>
                        <div class="flex justify-between"><span>Total Durasi (dtk)</span><span>{{ $row ? $f($row['total_durasi_dtk'], 2) : '-' }}</span></div>
                        <div class="flex justify-between"><span>Mesin</span><span>Rp {{ $row ? $f($row['mesin_cost'], 0) : '-' }}</span></div>
                        <div class="flex justify-between"><span>MP</span><span>Rp {{ $row ? $f($row['mp_cost'], 0) : '-' }}</span></div>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="flex justify-between font-semibold"><span>Total</span><span>Rp {{ $row ? $f($row['total_cost'], 0) : '-' }}</span></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Harga Material</div>
            <div class="p-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold">Jumlah Material</span>
                        <span class="rounded bg-slate-100 px-3 py-1 font-semibold">{{ $calc['harga_material']['jumlah_material'] ?? '-' }}</span>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Material</th>
                                    <th class="px-3 py-2 text-right">Berat Total (kg)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach (($calc['harga_material']['rows'] ?? []) as $idx => $row)
                                    <tr>
                                        <td class="px-3 py-2">{{ $idx + 1 }} Material</td>
                                        <td class="px-3 py-2">{{ $row['material'] }}</td>
                                        <td class="px-3 py-2 text-right">{{ $f($row['berat_total_kg'], 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold">Kurs Dollar</span>
                        <span class="rounded bg-slate-100 px-3 py-1 font-semibold">Rp {{ isset($calc) ? $f($calc['harga_material']['kurs_dollar'], 0) : '-' }}</span>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left" colspan="2">Origin per kg</th>
                                    <th class="px-3 py-2 text-left" colspan="2">Standarisasi per kg</th>
                                    <th class="px-3 py-2 text-right">Harga Origin</th>
                                    <th class="px-3 py-2 text-right">Harga Standarisasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach (($calc['harga_material']['price_rows'] ?? []) as $priceRow)
                                    <tr>
                                        <td class="px-3 py-2">USD</td>
                                        <td class="px-3 py-2">{{ is_null($priceRow['usd']) ? '-' : $f($priceRow['usd'], 1) }}</td>
                                        <td class="px-3 py-2">Rp {{ is_null($priceRow['origin_per_kg']) ? '-' : $f($priceRow['origin_per_kg'], 0) }}</td>
                                        <td class="px-3 py-2">Rp {{ is_null($priceRow['standard_per_kg']) ? '-' : $f($priceRow['standard_per_kg'], 0) }}</td>
                                        <td class="px-3 py-2 text-right">{{ is_null($priceRow['harga_origin'] ?? null) ? 'Rp -' : 'Rp '.$f($priceRow['harga_origin'], 0) }}</td>
                                        <td class="px-3 py-2 text-right">{{ is_null($priceRow['harga_standard'] ?? null) ? 'Rp -' : 'Rp '.$f($priceRow['harga_standard'], 0) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-100/60 font-semibold">
                                    <td class="px-3 py-2" colspan="4">Total</td>
                                    <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['harga_material']['total_harga_origin'], 0) : '-' }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['harga_material']['total_harga_standard'], 0) : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Total Harga Proses + Material</div>
            <div class="p-4">
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Komponen</th>
                                <th class="px-3 py-2 text-right">Harga Origin</th>
                                <th class="px-3 py-2 text-right">Harga Standarisasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-3 py-2">Harga Proses</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['harga_proses_origin'], 0) : '-' }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['harga_proses_standard'], 0) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2">Harga Material</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['harga_material_origin'], 0) : '-' }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['harga_material_standard'], 0) : '-' }}</td>
                            </tr>
                            <tr class="bg-slate-100/60 font-semibold">
                                <td class="px-3 py-2">Total</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['total_origin'], 0) : '-' }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ isset($calc) ? $f($calc['total_harga']['total_standard'], 0) : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('gpp-type');
            const mesinSelect = document.getElementById('gpp-mesin');
            const sizeSelect = document.getElementById('gpp-size');
            const quotationSelect = document.getElementById('gpp-quotation-id');
            const mainForm = document.getElementById('gpp-main-form');
            const wsType = document.getElementById('ws-type');
            const wsMesin = document.getElementById('ws-mesin');
            const wsSize = document.getElementById('ws-size');
            const wsBerat = document.getElementById('ws-berat');
            const wsKelebihan = document.getElementById('ws-kelebihan');
            const beratInput = document.querySelector('input[name="berat"]');
            const kelebihanInput = document.querySelector('input[name="kelebihan_pengiriman"]');
            const addItemBtn = document.getElementById('add-gpp-item-btn');
            const clearItemsBtn = document.getElementById('clear-gpp-items-btn');
            const toggleDetailBtn = document.getElementById('toggle-gpp-detail-btn');
            const detailSections = document.getElementById('gpp-detail-sections');
            const mainCalculationCard = document.getElementById('gpp-main-calculation-card');
            const itemsBody = document.getElementById('gpp-quotation-items-body');
            const mesinSizeMap = @json($mesinSizeMap);
            const typeMesinSizeMap = @json($typeMesinSizeMap);
            let preferredSize = @json($activeInput['size'] ?? $defaultSize);
            let autoCalcTimer = null;
            const currentCalc = @json(isset($calc) ? [
                'kode_barang' => $calc['kode_barang'] ?? '-',
                'total_standard' => $calc['total_harga']['total_standard'] ?? 0,
            ] : null);

            function syncSizeOptions() {
                const type = typeSelect.value;
                const mesin = mesinSelect.value;
                if (!mesin) {
                    sizeSelect.innerHTML = '';
                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = '-- Pilih Size --';
                    emptyOption.selected = true;
                    sizeSelect.appendChild(emptyOption);
                    return;
                }

                const allowedByTypeMesin = (typeMesinSizeMap[type] && typeMesinSizeMap[type][mesin]) || [];
                const allowedSizes = allowedByTypeMesin.length > 0 ? allowedByTypeMesin : (mesinSizeMap[mesin] || []);
                const currentSize = sizeSelect.value;
                const selectedSize = allowedSizes.includes(currentSize)
                    ? currentSize
                    : (allowedSizes.includes(preferredSize) ? preferredSize : '');

                sizeSelect.innerHTML = '';
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = '-- Pilih Size --';
                emptyOption.selected = selectedSize === '';
                sizeSelect.appendChild(emptyOption);

                allowedSizes.forEach(function (size) {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    option.selected = (size === selectedSize);
                    sizeSelect.appendChild(option);
                });

                preferredSize = null;
            }

            mesinSelect.addEventListener('change', syncSizeOptions);
            typeSelect.addEventListener('change', syncSizeOptions);
            function isAutoCalcReady() {
                const hasQuotation = !!(quotationSelect && quotationSelect.value);
                const hasType = !!(typeSelect && typeSelect.value);
                const hasMesin = !!(mesinSelect && mesinSelect.value);
                const hasSize = !!(sizeSelect && sizeSelect.value);
                const hasBerat = !!(beratInput && beratInput.value !== '');
                const hasKelebihan = !!(kelebihanInput && kelebihanInput.value !== '');
                return hasQuotation && hasType && hasMesin && hasSize && hasBerat && hasKelebihan;
            }

            function scheduleAutoCalculate() {
                if (!mainForm || !isAutoCalcReady()) return;
                if (autoCalcTimer) clearTimeout(autoCalcTimer);
                autoCalcTimer = setTimeout(function () {
                    mainForm.requestSubmit();
                }, 500);
            }

            function syncWorkspaceFromMain() {
                if (wsType) wsType.value = typeSelect?.value || '';
                if (wsMesin) wsMesin.value = mesinSelect?.value || '';
                if (wsSize) wsSize.value = sizeSelect?.value || '';
                if (wsBerat) wsBerat.value = beratInput?.value || '';
                if (wsKelebihan) wsKelebihan.value = kelebihanInput?.value || '';
            }

            function syncMainFromWorkspace() {
                if (typeSelect && wsType) typeSelect.value = wsType.value;
                if (mesinSelect && wsMesin) mesinSelect.value = wsMesin.value;
                syncSizeOptions();
                if (sizeSelect && wsSize) sizeSelect.value = wsSize.value;
                if (beratInput && wsBerat) beratInput.value = wsBerat.value;
                if (kelebihanInput && wsKelebihan) kelebihanInput.value = wsKelebihan.value;
            }

            wsType?.addEventListener('change', function () {
                syncMainFromWorkspace();
                scheduleAutoCalculate();
            });
            wsMesin?.addEventListener('change', function () {
                syncMainFromWorkspace();
                scheduleAutoCalculate();
            });
            wsSize?.addEventListener('change', function () {
                syncMainFromWorkspace();
                scheduleAutoCalculate();
            });
            wsBerat?.addEventListener('input', function () {
                syncMainFromWorkspace();
                scheduleAutoCalculate();
            });
            wsKelebihan?.addEventListener('input', function () {
                syncMainFromWorkspace();
                scheduleAutoCalculate();
            });

            quotationSelect?.addEventListener('change', function () {
                renderItemList();
                scheduleAutoCalculate();
            });
            document.getElementById('gpp-reset-btn')?.addEventListener('click', function () {
                typeSelect.value = '';
                mesinSelect.value = '';
                preferredSize = '';
                if (beratInput) beratInput.value = '';
                if (kelebihanInput) kelebihanInput.value = '';
                syncSizeOptions();
                typeSelect.dispatchEvent(new Event('change'));
                mesinSelect.dispatchEvent(new Event('change'));
                syncWorkspaceFromMain();
            });

            function getStorageKey() {
                return quotationSelect && quotationSelect.value ? `gpp_quote_items_${quotationSelect.value}` : null;
            }

            function getItems() {
                const key = getStorageKey();
                if (!key) return [];
                try {
                    return JSON.parse(localStorage.getItem(key) || '[]');
                } catch (_) {
                    return [];
                }
            }

            function saveItems(items) {
                const key = getStorageKey();
                if (!key) return;
                localStorage.setItem(key, JSON.stringify(items));
            }

            function renderItemList() {
                if (!itemsBody) return;
                const items = getItems();
                if (!items.length) {
                    itemsBody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada item.</td></tr>';
                    return;
                }
                itemsBody.innerHTML = '';
                items.forEach(function (item, idx) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-3 py-2">${idx + 1}</td>
                        <td class="px-3 py-2">${item.type || '-'}</td>
                        <td class="px-3 py-2">${item.mesin || '-'}</td>
                        <td class="px-3 py-2">${item.size || '-'}</td>
                        <td class="px-3 py-2 text-right">${item.berat || '-'}</td>
                        <td class="px-3 py-2">${item.kode_barang || '-'}</td>
                        <td class="px-3 py-2 text-right">${item.total_standard || '-'}</td>
                        <td class="px-3 py-2 text-center"><button type="button" data-remove="${idx}" class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Hapus</button></td>
                    `;
                    itemsBody.appendChild(tr);
                });
            }

            itemsBody?.addEventListener('click', function (event) {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;
                const idx = target.getAttribute('data-remove');
                if (idx === null) return;
                const items = getItems();
                items.splice(Number(idx), 1);
                saveItems(items);
                renderItemList();
            });

            addItemBtn?.addEventListener('click', function () {
                if (!quotationSelect || !quotationSelect.value) {
                    alert('Pilih quotation terlebih dahulu.');
                    return;
                }
                if (!currentCalc) {
                    alert('Hitung kalkulasi dulu sebelum menambah item.');
                    return;
                }
                const items = getItems();
                items.push({
                    type: typeSelect?.value || '',
                    mesin: mesinSelect?.value || '',
                    size: sizeSelect?.value || '',
                    berat: beratInput?.value || '',
                    kode_barang: currentCalc.kode_barang || '-',
                    total_standard: `Rp ${Number(currentCalc.total_standard || 0).toLocaleString('en-US')}`,
                });
                saveItems(items);
                renderItemList();
            });

            clearItemsBtn?.addEventListener('click', function () {
                const key = getStorageKey();
                if (!key) {
                    alert('Pilih quotation terlebih dahulu.');
                    return;
                }
                localStorage.removeItem(key);
                renderItemList();
            });

            toggleDetailBtn?.addEventListener('click', function () {
                if (!detailSections || !mainCalculationCard) return;
                const isHidden = detailSections.classList.contains('hidden');
                detailSections.classList.toggle('hidden', !isHidden);
                mainCalculationCard.classList.toggle('hidden', !isHidden);
                toggleDetailBtn.textContent = isHidden ? 'Sembunyikan Detail Kalkulasi' : 'Lihat Detail Kalkulasi';
            });

            syncSizeOptions();
            syncWorkspaceFromMain();
            renderItemList();
        });
    </script>
</x-app-layout>
