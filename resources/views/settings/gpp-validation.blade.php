<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-start justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Validasi Data GPP</h2>
                <p class="text-sm text-gray-600">List tabel validasi GPP dan preview data yang sudah masuk ke database.</p>
            </div>
            <a href="{{ route('setting') }}"
               class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to Settings
            </a>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3 rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">List Table GPP</h3>
                <div class="mt-4 space-y-2 max-h-[70vh] overflow-auto pr-1">
                    @forelse ($tables as $table)
                        <a href="{{ route('setting.gpp-validation', ['table' => $table]) }}"
                           class="block rounded-lg border px-3 py-2 text-sm {{ $selectedTable === $table ? 'border-indigo-300 bg-indigo-50 text-indigo-700 font-semibold' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                            {{ $table }}
                        </a>
                    @empty
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                            Belum ada tabel dengan suffix <code>_gpp</code>.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="xl:col-span-9 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <div>
                        <h3 class="font-semibold text-slate-900">
                            {{ $selectedTable !== '' ? $selectedTable : 'Pilih table di samping' }}
                        </h3>
                        @if ($selectedTable !== '')
                            <p class="text-xs text-slate-500">Menampilkan maksimal 200 baris dari total {{ number_format($totalRows) }} data.</p>
                        @endif
                    </div>
                </div>

                @if ($selectedTable === '')
                    <div class="px-4 py-6 text-sm text-slate-600">
                        Silakan klik salah satu nama tabel validasi GPP di panel kiri.
                    </div>
                @else
                    <div class="border-b border-slate-200 px-4 py-3 space-y-3">
                        @if ($editingRow)
                            <form method="POST" action="{{ route('setting.gpp-validation.update') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="table" value="{{ $selectedTable }}">
                                <input type="hidden" name="pk_value" value="{{ $editingRow[$primaryKey] ?? '' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <h4 class="font-semibold text-slate-900">Edit Data</h4>
                                    <a href="{{ route('setting.gpp-validation', ['table' => $selectedTable]) }}"
                                       class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Batal Edit
                                    </a>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                                    @foreach ($columnMeta as $meta)
                                        @php
                                            $name = $meta['name'];
                                            $isPk = ($primaryKey === $name);
                                            $isAuto = ($autoIncrementColumn === $name);
                                        @endphp
                                        @if (! $isPk && ! $isAuto)
                                            <label class="text-xs text-slate-600">
                                                <span class="font-semibold">{{ $name }}</span>
                                                <input type="text"
                                                       name="data[{{ $name }}]"
                                                       value="{{ old("data.$name", $editingRow[$name] ?? '') }}"
                                                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                                <button type="submit"
                                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                    Simpan Perubahan
                                </button>
                            </form>
                        @else
                            <details>
                                <summary class="cursor-pointer text-sm font-semibold text-slate-900">Tambah Data Baru</summary>
                                <form method="POST" action="{{ route('setting.gpp-validation.store') }}" class="mt-3 space-y-3">
                                    @csrf
                                    <input type="hidden" name="table" value="{{ $selectedTable }}">
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                                        @foreach ($columnMeta as $meta)
                                            @php
                                                $name = $meta['name'];
                                                $isAuto = ($autoIncrementColumn === $name);
                                            @endphp
                                            @if (! $isAuto)
                                                <label class="text-xs text-slate-600">
                                                    <span class="font-semibold">{{ $name }}</span>
                                                    <input type="text"
                                                           name="data[{{ $name }}]"
                                                           value="{{ old("data.$name", $meta['default']) }}"
                                                           class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                    <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                        Tambah Data
                                    </button>
                                </form>
                            </details>
                        @endif
                    </div>

                    <div class="overflow-auto max-h-[75vh]">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 sticky top-0">
                                <tr>
                                    @if ($primaryKey)
                                        <th class="px-3 py-2 text-left font-semibold text-slate-800 border-b border-slate-200 whitespace-nowrap">Aksi</th>
                                    @endif
                                    @foreach ($columns as $column)
                                        <th class="px-3 py-2 text-left font-semibold text-slate-800 border-b border-slate-200 whitespace-nowrap">
                                            {{ $column }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($rows as $row)
                                    <tr class="hover:bg-slate-50">
                                        @if ($primaryKey)
                                            <td class="px-3 py-2 align-top whitespace-nowrap">
                                                <a href="{{ route('setting.gpp-validation', ['table' => $selectedTable, 'edit' => $row[$primaryKey] ?? '']) }}"
                                                   class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    Edit
                                                </a>
                                            </td>
                                        @endif
                                        @foreach ($columns as $column)
                                            @php $value = $row[$column] ?? null; @endphp
                                            <td class="px-3 py-2 text-slate-700 align-top whitespace-nowrap">
                                                {{ is_scalar($value) || is_null($value) ? $value : json_encode($value) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-3 py-6 text-sm text-slate-500" colspan="{{ max(count($columns) + ($primaryKey ? 1 : 0), 1) }}">
                                            Tidak ada data pada tabel ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
