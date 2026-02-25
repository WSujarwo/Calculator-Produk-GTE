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
            'type' => $defaultType ?? '-- Select --',
            'mesin' => $defaultMesin ?? '-- Select --',
            'size' => $defaultSize ?? '-- Select --',
            'berat' => '-- Select --',
            'kelebihan_pengiriman' => '-- Select --',
        ];
        $activeInput = [
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

    <div class="w-full px-6 lg:px-10 py-6 space-y-6">
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-bold text-white">
                Dry Braided Gland Packing Calculation
            </div>
            <div class="bg-slate-50 border-b border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900">
                Type dan Size
            </div>

            <form class="p-4 lg:p-5 space-y-4" method="POST" action="{{ route('calculation.gpp.validate') }}">
                @csrf

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

                <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs text-indigo-700">
                    Kolom dengan badge <span class="font-semibold">Input</span> adalah field yang bisa diubah.
                </div>

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

                <div class="flex items-center justify-end gap-2">
                    <button id="gpp-reset-btn" type="button" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </button>
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Hitung Kalkulasi
                    </button>
                </div>
            </form>
        </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('gpp-type');
            const mesinSelect = document.getElementById('gpp-mesin');
            const sizeSelect = document.getElementById('gpp-size');
            const beratInput = document.querySelector('input[name="berat"]');
            const kelebihanInput = document.querySelector('input[name="kelebihan_pengiriman"]');
            const mesinSizeMap = @json($mesinSizeMap);
            const typeMesinSizeMap = @json($typeMesinSizeMap);
            let preferredSize = @json($activeInput['size'] ?? $defaultSize);

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
            document.getElementById('gpp-reset-btn')?.addEventListener('click', function () {
                typeSelect.value = '';
                mesinSelect.value = '';
                preferredSize = '';
                if (beratInput) beratInput.value = '';
                if (kelebihanInput) kelebihanInput.value = '';
                syncSizeOptions();
                typeSelect.dispatchEvent(new Event('change'));
                mesinSelect.dispatchEvent(new Event('change'));
            });

            syncSizeOptions();
        });
    </script>
</x-app-layout>
