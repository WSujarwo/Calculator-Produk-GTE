<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Materials</h2>
            <p class="text-sm text-gray-600">Import dan kelola data material</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4 text-gray-900">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <div class="font-semibold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-5">
            <form method="POST" action="{{ route('master.materials.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-7">
                    <label class="text-xs font-semibold text-gray-700">File Import (CSV/XLSX)</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    <p class="mt-1 text-xs text-gray-500">Part Number dipakai sebagai unique key (upsert).</p>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <a href="{{ route('master.materials.template') }}"
                        class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        <span class="material-symbols-rounded text-[20px]">download</span>
                        Format CSV
                    </a>
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    @can('master.materials.create')
                        <a href="{{ route('master.materials.create') }}"
                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                            <span class="material-symbols-rounded text-[20px]">add</span>
                            Create
                        </a>
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            <span class="material-symbols-rounded text-[20px]">upload</span>
                            Import / Upsert
                        </button>
                    @endcan
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-10">
                    <label class="text-xs font-semibold text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Part Number / Description / Naming / Code / Quality"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">search</span>
                        Filter
                    </button>
                    <a href="{{ route('master.materials.index') }}"
                        class="w-full inline-flex justify-center items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-900">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Part Number</th>
                            <th class="px-4 py-3 text-left font-semibold">Description</th>
                            <th class="px-4 py-3 text-left font-semibold">Naming</th>
                            <th class="px-4 py-3 text-left font-semibold">Code1</th>
                            <th class="px-4 py-3 text-left font-semibold">Code2</th>
                            <th class="px-4 py-3 text-left font-semibold">Code3</th>
                            <th class="px-4 py-3 text-left font-semibold">Thk</th>
                            <th class="px-4 py-3 text-left font-semibold">Quality</th>
                            <th class="px-4 py-3 text-left font-semibold">PriceSQM</th>
                            <th class="px-4 py-3 text-left font-semibold">PriceKG</th>
                            <th class="px-4 py-3 text-left font-semibold">PriceGram</th>
                            <th class="px-4 py-3 text-left font-semibold">Berat (gr)</th>
                            <th class="px-4 py-3 text-left font-semibold">Panjang (meter)</th>
                            <th class="px-4 py-3 text-left font-semibold">Berat per Meter (gr)</th>
                            <th class="px-4 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($materials as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3 font-semibold">{{ $row->part_number }}</td>
                                <td class="px-4 py-3">{{ $row->description }}</td>
                                <td class="px-4 py-3">{{ $row->naming }}</td>
                                <td class="px-4 py-3">{{ $row->code1 }}</td>
                                <td class="px-4 py-3">{{ $row->code2 }}</td>
                                <td class="px-4 py-3">{{ $row->code3 }}</td>
                                <td class="px-4 py-3">{{ $row->thk }}</td>
                                <td class="px-4 py-3">{{ $row->quality }}</td>
                                <td class="px-4 py-3">{{ $row->price_sqm }}</td>
                                <td class="px-4 py-3">{{ $row->price_kg }}</td>
                                <td class="px-4 py-3">{{ $row->price_gram }}</td>
                                <td class="px-4 py-3">{{ $row->berat_gr }}</td>
                                <td class="px-4 py-3">{{ $row->panjang_meter }}</td>
                                <td class="px-4 py-3">{{ $row->berat_per_meter_gr }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    @can('master.materials.edit')
                                        <a href="{{ route('master.materials.edit', $row) }}"
                                            class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 hover:bg-gray-50">
                                            <span class="material-symbols-rounded text-[18px]">edit</span>
                                            Edit
                                        </a>
                                    @endcan

                                    @can('master.materials.delete')
                                        <form action="{{ route('master.materials.destroy', $row) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                <span class="material-symbols-rounded text-[18px]">delete</span>
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-4 py-10 text-center text-gray-600">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-4">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
