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
            'skip_quotation_validation' => false,
            'type' => null,
            'mesin' => null,
            'size' => null,
            'berat' => null,
            'kelebihan_pengiriman' => null,
        ];
        $activeInput = [
            'quotation_id' => old('quotation_id', $activeInput['quotation_id'] ?? null),
            'skip_quotation_validation' => old('skip_quotation_validation', $activeInput['skip_quotation_validation'] ?? false),
            'type' => old('type', $activeInput['type'] ?? ''),
            'mesin' => old('mesin', $activeInput['mesin'] ?? ''),
            'size' => old('size', $activeInput['size'] ?? ''),
            'berat' => old('berat', $activeInput['berat'] ?? ''),
            'kelebihan_pengiriman' => old('kelebihan_pengiriman', $activeInput['kelebihan_pengiriman'] ?? ''),
        ];
        $areaOrder = ['diagonal_sudut', 'diagonal_tengah', 'corner', 'core', 'inner', 'core_cord'];
        $gpCardMap = [
            'Durasi Proses Press Gland Packing' => ['key' => 'press', 'label' => 'Press'],
            'Durasi Proses Gulung Gland Packing' => ['key' => 'gulung', 'label' => 'Gulung'],
            'Durasi Proses Packing Gland Packing' => ['key' => 'packing_box', 'label' => 'Packing Box'],
        ];
        $hasQuotationCreateError = $errors->has('quotation_date')
            || $errors->has('quotation_suffix')
            || $errors->has('company_id')
            || $errors->has('marketing_id')
            || $errors->has('result_status');
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
                <input type="hidden" id="gpp-skip-quotation-validation" name="skip_quotation_validation" value="{{ !empty($activeInput['skip_quotation_validation']) ? 1 : 0 }}">

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
                                                <option value="{{ $type }}" @selected(($activeInput['type'] ?? '') === $type)>{{ $type }}</option>
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
                                                <option value="{{ $mesin }}" @selected(($activeInput['mesin'] ?? '') === $mesin)>{{ $mesin }}</option>
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
                                                <option value="{{ $size }}" @selected(($activeInput['size'] ?? '') === $size)>{{ $size }}</option>
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
                                        <input type="number" step="0.01" min="0" name="berat" value="{{ $activeInput['berat'] ?? '' }}" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-indigo-50 px-3 py-2 font-semibold">
                                        *Input Kelebihan Saat Pengiriman (kg)
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Input</span>
                                    </td>
                                    <td class="bg-indigo-50 px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="kelebihan_pengiriman" value="{{ $activeInput['kelebihan_pengiriman'] ?? '' }}" class="w-full rounded-md border-indigo-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
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
                        <button id="open-create-quotation-modal-btn" type="button" class="inline-flex items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100">
                            Create Quotation
                        </button>
                    @endif
                </div>
                <div>
                    <button
                        id="ws-skip-quotation-btn"
                        type="button"
                        data-enabled="{{ !empty($activeInput['skip_quotation_validation']) ? 1 : 0 }}"
                        class="inline-flex items-center rounded-md border px-3 py-1 text-xs font-medium">
                        Mode Testing Quotation: {{ !empty($activeInput['skip_quotation_validation']) ? 'ON' : 'OFF' }}
                    </button>
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
                                                <option value="{{ $type }}" @selected(($activeInput['type'] ?? '') === $type)>{{ $type }}</option>
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
                                                <option value="{{ $mesin }}" @selected(($activeInput['mesin'] ?? '') === $mesin)>{{ $mesin }}</option>
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
                                                <option value="{{ $size }}" @selected(($activeInput['size'] ?? '') === $size)>{{ $size }}</option>
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
                        <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-3 py-3">
                            <button id="gpp-reset-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Reset Input
                            </button>
                            <button id="gpp-submit-btn" type="submit" form="gpp-main-form" class="inline-flex h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">
                                Hitung
                            </button>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">Point Hasil Kalkulasi</div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-3 py-2 text-slate-600">Kode Barang</td><td class="px-3 py-2 font-medium text-slate-900">{{ $calc['kode_barang'] ?? '-' }}</td></tr>
                                <tr><td class="px-3 py-2 text-slate-600">Berat + Spare (kg)</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? $f($calc['berat_plus_spare_kg'], 2) : '-' }}</td></tr>
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
                                <tr><td class="px-3 py-2 text-slate-600">Total HPP</td><td class="px-3 py-2 font-medium text-slate-900">{{ isset($calc) ? 'Rp '.$f($calc['total_harga']['total_standard'], 0) : '-' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">Daftar Produk Dalam Quotation Terpilih</h4>
                    <div class="flex w-full flex-wrap items-center justify-between gap-2 xl:w-auto xl:justify-end">
                        <div class="flex flex-wrap items-center gap-2">
                            <button id="clear-gpp-items-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Kosongkan Daftar
                            </button>
                            <button id="toggle-gpp-detail-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Lihat Detail Kalkulasi
                            </button>
                        </div>
                        <button id="add-gpp-item-btn" type="button" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50" {{ isset($calc) ? '' : 'disabled' }}>
                            Tambah Produk Ini
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

    <div id="create-quotation-modal" class="fixed inset-0 z-50 {{ $hasQuotationCreateError ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl lg:p-8">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Quotation</h3>
                <button id="close-create-quotation-modal-btn" type="button" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <form action="{{ route('quotations.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="return_to" value="gpp">

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Quotation No</label>
                        <div class="mt-2 grid grid-cols-[170px,1fr] gap-2">
                            <input type="text" value="GTE-QTN-" class="w-full rounded-xl border-slate-300 bg-slate-100 text-sm text-slate-500" disabled>
                            <input type="text" name="quotation_suffix" value="{{ old('quotation_suffix') }}" maxlength="6" placeholder="XXXXXX" class="w-full rounded-xl border-slate-300 text-sm uppercase focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        @error('quotation_suffix')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Result Status</label>
                        <input type="hidden" name="result_status" value="PENDING">
                        <select class="mt-2 w-full rounded-xl border-slate-300 bg-slate-100 text-sm text-slate-500" disabled>
                            <option selected>PENDING</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Date</label>
                            <input type="date" name="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('quotation_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Marketing</label>
                                <button type="button" id="openMarketingModal" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">+ Add Marketing</button>
                            </div>
                            <select name="marketing_id" id="createMarketingId" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">-- Select marketing --</option>
                                @foreach (($marketingOptions ?? []) as $opt)
                                    <option value="{{ $opt['id'] }}" @selected((string) old('marketing_id') === (string) $opt['id'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                            @error('marketing_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Revision No</label>
                            <input type="number" min="0" name="revision_no" value="{{ old('revision_no', 0) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Customer Name</label>
                                <button type="button" id="openCustomerModal" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">+ Add Customer</button>
                            </div>
                            <select name="company_id" id="createCompanyId" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">-- Select customer --</option>
                                @foreach (($companyOptions ?? []) as $opt)
                                    <option value="{{ $opt['id'] }}" data-address="{{ $opt['address'] }}" @selected((string) old('company_id') === (string) $opt['id'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                            @error('company_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Address</label>
                            <textarea name="company_address" id="createCompanyAddress" rows="4" class="mt-2 w-full rounded-xl border-slate-300 text-sm">{{ old('company_address') }}</textarea>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700">Attention</label><input type="text" name="attention" value="{{ old('attention') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery To</label><input type="text" name="delivery_to" value="{{ old('delivery_to') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery Term</label><input type="text" name="delivery_term" value="{{ old('delivery_term') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="payment_days" value="{{ old('payment_days') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after delivery & invoice</span></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="delivery_time_days" value="{{ old('delivery_time_days') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after PO received</span></div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Scope of Work</label>
                            <select name="scope_of_work" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                                @foreach (['Supply Only', 'Supply + Installation', 'Installation Only'] as $scope)
                                    <option value="{{ $scope }}" @selected(old('scope_of_work', 'Supply Only') === $scope)>{{ $scope }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="price_validity_weeks" value="{{ old('price_validity_weeks') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">weeks after quotation date</span></div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button id="cancel-create-quotation-modal-btn" type="button" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Close</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>

    <div id="customerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Add Customer</h3>
                <button type="button" data-close-modal="customerModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <form id="customerInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="company_code" placeholder="Customer Code" class="w-full rounded-xl border-slate-300 text-sm" required>
                <input type="text" name="company_name" placeholder="Customer Name" class="w-full rounded-xl border-slate-300 text-sm" required>
                <textarea name="address" placeholder="Address" rows="3" class="w-full rounded-xl border-slate-300 text-sm"></textarea>
                <div class="grid gap-3 md:grid-cols-2">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-slate-300 text-sm">
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <p id="customerInlineError" class="hidden text-sm text-red-600"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="customerModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-700">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="marketingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Add Marketing</h3>
                <button type="button" data-close-modal="marketingModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <form id="marketingInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="marketing_no" placeholder="Marketing No" class="w-full rounded-xl border-slate-300 text-sm" required>
                <input type="text" name="name" placeholder="Name" class="w-full rounded-xl border-slate-300 text-sm" required>
                <div class="grid gap-3 md:grid-cols-2">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-slate-300 text-sm" required>
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <p id="marketingInlineError" class="hidden text-sm text-red-600"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="marketingModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-700">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('gpp-type');
            const mesinSelect = document.getElementById('gpp-mesin');
            const sizeSelect = document.getElementById('gpp-size');
            const quotationSelect = document.getElementById('gpp-quotation-id');
            const skipQuotationInput = document.getElementById('gpp-skip-quotation-validation');
            const wsSkipQuotationBtn = document.getElementById('ws-skip-quotation-btn');
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
            const createQuotationModal = document.getElementById('create-quotation-modal');
            const openCreateQuotationModalBtn = document.getElementById('open-create-quotation-modal-btn');
            const closeCreateQuotationModalBtn = document.getElementById('close-create-quotation-modal-btn');
            const cancelCreateQuotationModalBtn = document.getElementById('cancel-create-quotation-modal-btn');
            const createCompanyId = document.getElementById('createCompanyId');
            const createCompanyAddress = document.getElementById('createCompanyAddress');
            const createMarketingId = document.getElementById('createMarketingId');
            const mesinSizeMap = @json($mesinSizeMap);
            let preferredSize = @json($activeInput['size'] ?? '');
            let autoCalcTimer = null;
            const draftStorageKey = 'gpp_workspace_draft_v1';
            const gppIndexUrl = @json(route('calculation.gpp'));
            const currentCalc = @json(isset($calc) ? [
                'kode_barang' => $calc['kode_barang'] ?? '-',
                'total_standard' => $calc['total_harga']['total_standard'] ?? 0,
            ] : null);

            function isSkipQuotationEnabled() {
                return !!(skipQuotationInput && skipQuotationInput.value === '1');
            }

            function renderSkipQuotationButton() {
                if (!wsSkipQuotationBtn) return;
                const enabled = isSkipQuotationEnabled();
                wsSkipQuotationBtn.dataset.enabled = enabled ? '1' : '0';
                wsSkipQuotationBtn.textContent = `Mode Testing Quotation: ${enabled ? 'ON' : 'OFF'}`;
                wsSkipQuotationBtn.className = enabled
                    ? 'mt-2 inline-flex items-center rounded-md border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700'
                    : 'mt-2 inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1 text-xs font-medium text-slate-600';
            }

            function openCreateQuotationModal() {
                if (!createQuotationModal) return;
                createQuotationModal.classList.remove('hidden');
                createQuotationModal.classList.add('flex');
            }

            function closeCreateQuotationModal() {
                if (!createQuotationModal) return;
                createQuotationModal.classList.remove('flex');
                createQuotationModal.classList.add('hidden');
            }

            function saveWorkspaceDraft() {
                const draft = {
                    quotation_id: quotationSelect?.value || '',
                    skip_quotation_validation: isSkipQuotationEnabled() ? 1 : 0,
                    type: wsType?.value || '',
                    mesin: wsMesin?.value || '',
                    size: wsSize?.value || '',
                    berat: wsBerat?.value || '',
                    kelebihan: wsKelebihan?.value || '',
                };
                localStorage.setItem(draftStorageKey, JSON.stringify(draft));
            }

            function loadWorkspaceDraft() {
                try {
                    const raw = localStorage.getItem(draftStorageKey);
                    if (!raw) return;
                    const draft = JSON.parse(raw);
                    if (!draft || typeof draft !== 'object') return;

                    const currentQuotation = quotationSelect?.value || '';
                    const draftQuotation = draft.quotation_id || '';
                    const skipFromDraft = Number(draft.skip_quotation_validation || 0) === 1;
                    if (!skipFromDraft && currentQuotation !== draftQuotation) return;

                    if (skipQuotationInput) skipQuotationInput.value = skipFromDraft ? '1' : '0';
                    renderSkipQuotationButton();

                    if (wsType && draft.type) wsType.value = draft.type;
                    if (wsMesin && draft.mesin) wsMesin.value = draft.mesin;
                    syncMainFromWorkspace();
                    if (wsSize && draft.size) wsSize.value = draft.size;
                    if (wsBerat && draft.berat !== '') wsBerat.value = draft.berat;
                    if (wsKelebihan && draft.kelebihan !== '') wsKelebihan.value = draft.kelebihan;
                    syncMainFromWorkspace();
                } catch (_) {
                    // ignore invalid draft
                }
            }

            function syncSizeOptions() {
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

                const allowedSizes = mesinSizeMap[mesin] || [];
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

            function syncWorkspaceSizeOptions() {
                if (!wsSize || !sizeSelect) return;
                const availableSizes = Array.from(sizeSelect.options)
                    .map(function (opt) { return opt.value; })
                    .filter(function (v) { return v !== ''; });
                const currentWsSize = wsSize.value;

                wsSize.innerHTML = '';
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = '-- Pilih Size --';
                wsSize.appendChild(emptyOption);

                availableSizes.forEach(function (size) {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    wsSize.appendChild(option);
                });

                wsSize.value = availableSizes.includes(currentWsSize)
                    ? currentWsSize
                    : (sizeSelect.value || '');
            }

            mesinSelect.addEventListener('change', syncSizeOptions);
            function isAutoCalcReady() {
                const skipQuotation = isSkipQuotationEnabled();
                const hasQuotation = skipQuotation || !!(quotationSelect && quotationSelect.value);
                const hasType = !!(typeSelect && typeSelect.value);
                const hasMesin = !!(mesinSelect && mesinSelect.value);
                const hasSize = !!(sizeSelect && sizeSelect.value);
                const hasBerat = !!(beratInput && beratInput.value !== '');
                const hasKelebihan = !!(kelebihanInput && kelebihanInput.value !== '');
                const isSizeAllowed = hasSize && Array.from(sizeSelect.options).some(function (opt) {
                    return opt.value === sizeSelect.value;
                });
                return hasQuotation && hasType && hasMesin && hasSize && isSizeAllowed && hasBerat && hasKelebihan;
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
                syncWorkspaceSizeOptions();
                if (wsSize) wsSize.value = sizeSelect?.value || '';
                if (wsBerat) wsBerat.value = beratInput?.value || '';
                if (wsKelebihan) wsKelebihan.value = kelebihanInput?.value || '';
            }

            function syncMainFromWorkspace() {
                if (typeSelect && wsType) typeSelect.value = wsType.value;
                if (mesinSelect && wsMesin) mesinSelect.value = wsMesin.value;
                syncSizeOptions();
                syncWorkspaceSizeOptions();
                if (sizeSelect && wsSize) {
                    sizeSelect.value = wsSize.value;
                    wsSize.value = sizeSelect.value;
                }
                if (beratInput && wsBerat) beratInput.value = wsBerat.value;
                if (kelebihanInput && wsKelebihan) kelebihanInput.value = wsKelebihan.value;
            }

            wsType?.addEventListener('change', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
            });
            wsMesin?.addEventListener('change', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
            });
            wsSize?.addEventListener('change', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
            });
            wsBerat?.addEventListener('input', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
            });
            wsKelebihan?.addEventListener('input', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
                scheduleAutoCalculate();
            });
            wsKelebihan?.addEventListener('change', function () {
                syncMainFromWorkspace();
                saveWorkspaceDraft();
                scheduleAutoCalculate();
            });

            wsSkipQuotationBtn?.addEventListener('click', function () {
                const enabled = !isSkipQuotationEnabled();
                if (skipQuotationInput) skipQuotationInput.value = enabled ? '1' : '0';
                renderSkipQuotationButton();
                saveWorkspaceDraft();
            });

            openCreateQuotationModalBtn?.addEventListener('click', openCreateQuotationModal);
            closeCreateQuotationModalBtn?.addEventListener('click', closeCreateQuotationModal);
            cancelCreateQuotationModalBtn?.addEventListener('click', closeCreateQuotationModal);
            createQuotationModal?.addEventListener('click', function (event) {
                if (event.target === createQuotationModal) closeCreateQuotationModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeCreateQuotationModal();
            });

            function toggleModal(id, show) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.toggle('hidden', !show);
                modal.classList.toggle('flex', show);
            }

            createCompanyId?.addEventListener('change', function () {
                const selected = createCompanyId.options[createCompanyId.selectedIndex];
                const address = selected?.getAttribute('data-address') || '';
                if (createCompanyAddress && !createCompanyAddress.value) {
                    createCompanyAddress.value = address;
                }
            });

            document.getElementById('openCustomerModal')?.addEventListener('click', function () {
                toggleModal('customerModal', true);
            });
            document.getElementById('openMarketingModal')?.addEventListener('click', function () {
                toggleModal('marketingModal', true);
            });
            document.querySelectorAll('[data-close-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    toggleModal(this.getAttribute('data-close-modal'), false);
                });
            });

            async function postInline(formId, url, errorId, onSuccess) {
                const form = document.getElementById(formId);
                const error = document.getElementById(errorId);
                if (!form || !error) return;
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    error.classList.add('hidden');
                    error.textContent = '';
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Failed to save data.');
                        onSuccess(data);
                        form.reset();
                    } catch (errorObj) {
                        error.textContent = errorObj.message;
                        error.classList.remove('hidden');
                    }
                });
            }

            postInline(
                'customerInlineForm',
                @json(route('quotations.inline-companies.store')),
                'customerInlineError',
                function (data) {
                    if (createCompanyId) {
                        const option = document.createElement('option');
                        option.value = String(data.id);
                        option.textContent = data.label;
                        option.setAttribute('data-address', data.address || '');
                        createCompanyId.appendChild(option);
                        createCompanyId.value = String(data.id);
                    }
                    if (createCompanyAddress) {
                        createCompanyAddress.value = data.address || '';
                    }
                    toggleModal('customerModal', false);
                }
            );

            postInline(
                'marketingInlineForm',
                @json(route('quotations.inline-marketings.store')),
                'marketingInlineError',
                function (data) {
                    if (createMarketingId) {
                        const option = document.createElement('option');
                        option.value = String(data.id);
                        option.textContent = data.label;
                        createMarketingId.appendChild(option);
                        createMarketingId.value = String(data.id);
                    }
                    toggleModal('marketingModal', false);
                }
            );

            quotationSelect?.addEventListener('change', function () {
                renderItemList();
                saveWorkspaceDraft();
            });
            document.getElementById('gpp-reset-btn')?.addEventListener('click', function () {
                localStorage.removeItem(draftStorageKey);
                const quotationId = quotationSelect?.value || '';
                const target = quotationId
                    ? `${gppIndexUrl}?quotation_id=${encodeURIComponent(quotationId)}`
                    : gppIndexUrl;
                window.location.href = target;
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
                    const payload = encodeURIComponent(JSON.stringify({
                        type: item.type || '',
                        mesin: item.mesin || '',
                        size: item.size || '',
                        berat: item.berat || '',
                        kelebihan: item.kelebihan || '',
                    }));
                    tr.innerHTML = `
                        <td class="px-3 py-2">${idx + 1}</td>
                        <td class="px-3 py-2">${item.type || '-'}</td>
                        <td class="px-3 py-2">${item.mesin || '-'}</td>
                        <td class="px-3 py-2">${item.size || '-'}</td>
                        <td class="px-3 py-2 text-right">${item.berat || '-'}</td>
                        <td class="px-3 py-2">${item.kode_barang || '-'}</td>
                        <td class="px-3 py-2 text-right">${item.total_standard || '-'}</td>
                        <td class="px-3 py-2 text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" data-detail-payload="${payload}" class="rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50">Detail</button>
                                <button type="button" data-remove="${idx}" class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 hover:bg-rose-50">Hapus</button>
                            </div>
                        </td>
                    `;
                    itemsBody.appendChild(tr);
                });
            }

            function setSelectValueSafe(selectEl, value) {
                if (!selectEl) return;
                const raw = String(value || '');
                const normalized = raw.padStart(2, '0');
                const compact = String(Number(raw));
                const candidates = [raw, normalized, compact];
                const found = candidates.find(function (candidate) {
                    return Array.from(selectEl.options).some(function (opt) { return opt.value === candidate; });
                });
                selectEl.value = found || '';
            }

            itemsBody?.addEventListener('click', function (event) {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;
                const idx = target.getAttribute('data-remove');
                if (idx !== null) {
                    const items = getItems();
                    items.splice(Number(idx), 1);
                    saveItems(items);
                    renderItemList();
                    return;
                }

                const detailPayload = target.getAttribute('data-detail-payload');
                if (!detailPayload) return;
                let item = null;
                try {
                    item = JSON.parse(decodeURIComponent(detailPayload));
                } catch (_) {
                    return;
                }

                setSelectValueSafe(wsType, item.type || '');
                setSelectValueSafe(wsMesin, item.mesin || '');
                syncMainFromWorkspace();
                setSelectValueSafe(wsSize, item.size || '');
                if (wsBerat) wsBerat.value = item.berat || '';
                if (wsKelebihan) wsKelebihan.value = item.kelebihan || '';
                syncMainFromWorkspace();
                scheduleAutoCalculate();
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
                    kelebihan: kelebihanInput?.value || '',
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
            syncWorkspaceSizeOptions();
            syncWorkspaceFromMain();
            renderSkipQuotationButton();
            loadWorkspaceDraft();
            renderItemList();
        });
    </script>
</x-app-layout>
