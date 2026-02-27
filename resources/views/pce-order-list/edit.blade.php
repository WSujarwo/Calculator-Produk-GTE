<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit PCE Order Item</h2>
            <p class="text-sm text-gray-600">Ubah data item order.</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('pce-orderlist.update', $item) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('pce-order-list._form')
                <div class="flex justify-end gap-2 border-t border-slate-200 pt-3">
                    <a href="{{ route('pce-orderlist.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</a>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
