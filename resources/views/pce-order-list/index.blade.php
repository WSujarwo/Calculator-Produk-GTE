<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">PCE Order List</h2>
            <p class="text-sm text-gray-600">Calculation / EJM / List Order</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <form method="GET" action="{{ route('pce-orderlist.index') }}" class="grid grid-cols-1 gap-2 md:grid-cols-3">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari PCE / Plat / Description"
                       class="rounded-lg border-slate-300 text-sm">

                <select name="pce_header_id" class="rounded-lg border-slate-300 text-sm">
                    <option value="">-- Semua Nomor PCE --</option>
                    @foreach ($pceHeaders as $header)
                        <option value="{{ $header->id }}"
                            @selected((string) request('pce_header_id') === (string) $header->id)>
                            {{ $header->pce_number }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Search
                    </button>
                    <a href="{{ route('pce-orderlist.index') }}"
                       class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                        Reset
                    </a>
                </div>
            </form>

            <a href="{{ route('pce-orderlist.create') }}"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Create
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1800px] text-sm">
                    <thead class="bg-slate-100 text-slate-900">
                        <tr>
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">Nomor PCE</th>
                            <th class="px-3 py-2 text-left">Plat Number</th>
                            <th class="px-3 py-2 text-left">Description</th>
                            <th class="px-3 py-2 text-right">Qty</th>
                            <th class="px-3 py-2 text-left">Shape</th>
                            <th class="px-3 py-2 text-left">Type EJM</th>
                            <th class="px-3 py-2 text-right">NB</th>
                            <th class="px-3 py-2 text-right">Noc</th>
                            <th class="px-3 py-2 text-right">ID</th>
                            <th class="px-3 py-2 text-right">OD</th>
                            <th class="px-3 py-2 text-right">Thk</th>
                            <th class="px-3 py-2 text-right">Ply</th>
                            <th class="px-3 py-2 text-left">Material Bellow</th>
                            <th class="px-3 py-2 text-left">Material Flange</th>
                            <th class="px-3 py-2 text-left">Material Pipe End</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $idx => $item)
                            <tr class="border-t border-slate-100 hover:bg-slate-50">
                                <td class="px-3 py-2">
                                    {{ $items->firstItem() + $idx }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->header?->pce_number ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->plat_number }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->description ?: '-' }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    {{ number_format((float) $item->qty, 2, '.', ',') }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->shape?->shape_name ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->typeConfig?->type_name ?? '-' }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    {{ $item->nb !== null ? number_format((float) $item->nb, 2, '.', ',') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    {{ $item->noc !== null ? number_format((float) $item->noc, 2, '.', ',') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    {{ $item->id_mm !== null ? number_format((float) $item->id_mm, 2, '.', ',') : '-' }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    {{ $item->od_mm !== null ? number_format((float) $item->od_mm, 2, '.', ',') : '-' }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    {{ $item->thk_mm !== null ? number_format((float) $item->thk_mm, 2, '.', ',') : '-' }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    {{ $item->ply !== null ? number_format((float) $item->ply, 2, '.', ',') : '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->materialBellow?->part_number ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->materialFlange?->part_number ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $item->materialPipeEnd?->part_number ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    <div class="flex gap-1">
                                        <a href="{{ route('pce-orderlist.show', $item) }}"
                                           class="rounded bg-slate-700 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-800">
                                            View
                                        </a>

                                        <a href="{{ route('pce-orderlist.edit', $item) }}"
                                           class="rounded bg-indigo-600 px-2 py-1 text-xs font-semibold text-white hover:bg-indigo-700">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('pce-orderlist.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="px-3 py-6 text-center text-slate-500">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>