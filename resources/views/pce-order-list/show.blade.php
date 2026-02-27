<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Detail PCE Order Item</h2>
            <p class="text-sm text-gray-600">Lihat detail item order.</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div><span class="font-semibold">Nomor PCE:</span> {{ $item->header?->pce_number ?? '-' }}</div>
                <div><span class="font-semibold">Plat Number:</span> {{ $item->plat_number }}</div>
                <div><span class="font-semibold">Description:</span> {{ $item->description ?: '-' }}</div>
                <div><span class="font-semibold">Qty:</span> {{ $item->qty }}</div>
                <div><span class="font-semibold">Shape:</span> {{ $item->shape?->shape_name ?? '-' }}</div>
                <div><span class="font-semibold">Type EJM:</span> {{ $item->typeConfig?->type_name ?? '-' }}</div>
                <div><span class="font-semibold">NB:</span> {{ $item->nb ?? '-' }}</div>
                <div><span class="font-semibold">ID:</span> {{ $item->id_mm ?? '-' }}</div>
                <div><span class="font-semibold">OD:</span> {{ $item->od_mm ?? '-' }}</div>
                <div><span class="font-semibold">Thk:</span> {{ $item->thk_mm ?? '-' }}</div>
                <div><span class="font-semibold">Ply:</span> {{ $item->ply ?? '-' }}</div>
                <div><span class="font-semibold">Material Bellow:</span> {{ $item->materialBellow?->part_number ?? '-' }}</div>
                <div><span class="font-semibold">Material Flange:</span> {{ $item->materialFlange?->part_number ?? '-' }}</div>
                <div><span class="font-semibold">Material Pipe End:</span> {{ $item->materialPipeEnd?->part_number ?? '-' }}</div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-200 pt-3">
                <a href="{{ route('pce-orderlist.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Back</a>
                <a href="{{ route('pce-orderlist.edit', $item) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Edit</a>
            </div>
        </div>
    </div>
</x-app-layout>
