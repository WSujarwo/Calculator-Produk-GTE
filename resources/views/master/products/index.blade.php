<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Master Products</h2>
            <p class="text-sm text-gray-600">Kelola product (EJM/RTI/GPP/dll)</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4 text-gray-900">

        {{-- Alerts --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
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

        {{-- Filter Card (Create sejajar dengan Filter + Reset) --}}
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-6">
                    <label class="text-xs font-semibold text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari product_code / product_name..."
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-3">
                    <label class="text-xs font-semibold text-gray-700">Status</label>
                    <select name="is_active"
                            class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        <option value="">All</option>
                        <option value="1" @selected(request('is_active')==='1')>Active</option>
                        <option value="0" @selected(request('is_active')==='0')>Inactive</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">search</span>
                        Filter
                    </button>

                    <a href="{{ route('master.products.index') }}"
                       class="w-full inline-flex justify-center items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        Reset
                    </a>

                    <a href="{{ route('master.products.create') }}"
                       class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                        <span class="material-symbols-rounded text-[20px]">add</span>
                        Create
                    </a>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-900">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">ID</th>
                            <th class="px-4 py-3 text-left font-semibold">Code</th>
                            <th class="px-4 py-3 text-left font-semibold">Name</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $p)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">{{ $p->id }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $p->product_code }}</td>
                                <td class="px-4 py-3">{{ $p->product_name }}</td>
                                <td class="px-4 py-3">
                                    @if($p->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('master.products.edit', $p) }}"
                                       class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 hover:bg-gray-50">
                                        <span class="material-symbols-rounded text-[18px]">edit</span>
                                        Edit
                                    </a>

                                    <form action="{{ route('master.products.destroy', $p) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Yakin hapus product ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                            <span class="material-symbols-rounded text-[18px]">delete</span>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-600">
                                    Tidak ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>