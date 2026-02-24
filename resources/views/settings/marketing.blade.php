<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">List Marketing</h2>
            <p class="text-sm text-gray-600">Input, update, dan delete data marketing.</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-6">
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

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">
                    {{ $editing ? 'Update Marketing' : 'Tambah Marketing' }}
                </h3>
                @if($editing)
                    <a href="{{ route('setting.marketing.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Batal edit</a>
                @endif
            </div>

            <form method="POST"
                  action="{{ $editing ? route('setting.marketing.update', $editing['id']) : route('setting.marketing.store') }}"
                  class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif

                <input type="text" name="name" placeholder="Nama marketing"
                       value="{{ old('name', $editing['name'] ?? '') }}"
                       class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300">

                <input type="email" name="email" placeholder="Email"
                       value="{{ old('email', $editing['email'] ?? '') }}"
                       class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300">

                <input type="text" name="phone" placeholder="No. telp (opsional)"
                       value="{{ old('phone', $editing['phone'] ?? '') }}"
                       class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300">

                <div class="md:col-span-3">
                    <button type="submit"
                            class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        {{ $editing ? 'Update Marketing' : 'Simpan Marketing' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Data Marketing</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Nama</th>
                            <th class="px-3 py-2 text-left font-semibold">Email</th>
                            <th class="px-3 py-2 text-left font-semibold">No. Telp</th>
                            <th class="px-3 py-2 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($marketingList as $marketing)
                            <tr>
                                <td class="px-3 py-3 font-semibold text-gray-900">{{ $marketing['name'] }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $marketing['email'] }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $marketing['phone'] }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('setting.marketing.index', ['edit' => $marketing['id']]) }}"
                                           class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('setting.marketing.destroy', $marketing['id']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Yakin hapus marketing ini?')"
                                                    class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-500">
                                    Belum ada data marketing.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

