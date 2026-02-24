<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Cost Product</h2>
            <p class="text-sm text-gray-600">Import dan kelola data cost product</p>
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
            <form method="POST" action="{{ route('master.cost-products.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-7">
                    <label class="text-xs font-semibold text-gray-700">File Import (CSV/XLSX)</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    <p class="mt-1 text-xs text-gray-500">Gunakan format kolom dari template. File CSV bisa dibuka/diisi di Excel lalu di-save ulang.</p>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <a href="{{ route('master.cost-products.template') }}"
                       class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        <span class="material-symbols-rounded text-[20px]">download</span>
                        Format CSV
                    </a>
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    @can('master.cost-products.create')
                        <a href="{{ route('master.cost-products.create') }}"
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
                <div class="md:col-span-8">
                    <label class="text-xs font-semibold text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari DLABORNO / GLACCOUNT / ACCOUNTNAME / DESCRIPTION"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-700">Status</label>
                    <select name="status"
                            class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        <option value="">All</option>
                        <option value="1" @selected(request('status')==='1')>1</option>
                        <option value="0" @selected(request('status')==='0')>0</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">search</span>
                        Filter
                    </button>
                    <a href="{{ route('master.cost-products.index') }}"
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
                            <th class="px-4 py-3 text-left font-semibold">DLABORNO</th>
                            <th class="px-4 py-3 text-left font-semibold">COST</th>
                            <th class="px-4 py-3 text-left font-semibold">GLACCOUNT</th>
                            <th class="px-4 py-3 text-left font-semibold">STATUS</th>
                            <th class="px-4 py-3 text-left font-semibold">ACCOUNTNAME</th>
                            <th class="px-4 py-3 text-left font-semibold">Statuse</th>
                            <th class="px-4 py-3 text-left font-semibold">DESCRIPTION</th>
                            <th class="px-4 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($costProducts as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3 font-semibold">{{ $row->dlaborno }}</td>
                                <td class="px-4 py-3">{{ $row->cost !== null ? 'Rp ' . number_format((float) $row->cost, 0, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3">{{ $row->glaccount }}</td>
                                <td class="px-4 py-3">{{ $row->status }}</td>
                                <td class="px-4 py-3">{{ $row->accountname }}</td>
                                <td class="px-4 py-3">{{ $row->statuse }}</td>
                                <td class="px-4 py-3">{{ $row->description }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    @can('master.cost-products.edit')
                                        <a href="{{ route('master.cost-products.edit', $row) }}"
                                           class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 hover:bg-gray-50">
                                            <span class="material-symbols-rounded text-[18px]">edit</span>
                                            Edit
                                        </a>
                                    @endcan

                                    @can('master.cost-products.delete')
                                        <form action="{{ route('master.cost-products.destroy', $row) }}"
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
                                <td colspan="8" class="px-4 py-10 text-center text-gray-600">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-4">
                {{ $costProducts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
