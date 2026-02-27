<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-start justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Validasi Material EJM</h2>
                <p class="text-sm text-gray-600">Sumber dropdown material EJM (Bellow, Pipe Nipple, Flange).</p>
            </div>
            <a href="{{ route('setting') }}"
               class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to Settings
            </a>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <aside class="xl:col-span-3 rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Data Validasi EJM</h3>
                <div class="mt-4 space-y-2">
                    @foreach ($validationMenus as $menu)
                        @php $isActive = ($activeTab === $menu['key']); @endphp
                        <a href="{{ $menu['url'] }}"
                           class="block rounded-lg border px-3 py-2 text-sm {{ $isActive ? 'border-amber-300 bg-amber-50 text-amber-700 font-semibold' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                            {{ $menu['label'] }}
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="xl:col-span-9 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <form method="GET" action="{{ route('setting.ejm-validation-material.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <label class="text-xs text-slate-600">
                            <span class="font-semibold">Component</span>
                            <select name="component" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                <option value="">-- All --</option>
                                @foreach (['Bellow', 'Pipe - Nipple', 'Flange', 'Collar'] as $component)
                                    <option value="{{ $component }}" @selected(request('component') === $component)>{{ $component }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-xs text-slate-600 md:col-span-2">
                            <span class="font-semibold">Search</span>
                            <input type="text" name="q" value="{{ request('q') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm" placeholder="material / part number / naming">
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
                            <a href="{{ route('setting.ejm-validation-material.index') }}" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold text-slate-900">Data Validasi Material</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('setting.ejm-validation-material.template.csv') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Template CSV</a>
                        <a href="{{ route('setting.ejm-validation-material.template.excel') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Template Excel</a>
                        <button type="button" id="openImportModal"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import CSV/XLSX</button>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                    <table class="min-w-[1100px] text-sm border-collapse">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-100 text-slate-900">
                                <th class="px-3 py-2 border border-slate-300">Component</th>
                                <th class="px-3 py-2 border border-slate-300">Material</th>
                                <th class="px-3 py-2 border border-slate-300">Thk</th>
                                <th class="px-3 py-2 border border-slate-300">Ply</th>
                                <th class="px-3 py-2 border border-slate-300">Size</th>
                                <th class="px-3 py-2 border border-slate-300">Sch</th>
                                <th class="px-3 py-2 border border-slate-300">Type</th>
                                <th class="px-3 py-2 border border-slate-300">Part Number</th>
                                <th class="px-3 py-2 border border-slate-300">Naming</th>
                                <th class="px-3 py-2 border border-slate-300">Code1</th>
                                <th class="px-3 py-2 border border-slate-300">Code2</th>
                                <th class="px-3 py-2 border border-slate-300">Code3</th>
                                <th class="px-3 py-2 border border-slate-300">Quality</th>
                                <th class="px-3 py-2 border border-slate-300">Price SQM</th>
                                <th class="px-3 py-2 border border-slate-300">Price KG</th>
                                <th class="px-3 py-2 border border-slate-300">Price Gram</th>
                                <th class="px-3 py-2 border border-slate-300">Berat (gr)</th>
                                <th class="px-3 py-2 border border-slate-300">Panjang (m)</th>
                                <th class="px-3 py-2 border border-slate-300">Berat/m (gr)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->component }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->material }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->thk_mm }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->ply }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->size_in }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->sch }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->type }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->part_number }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->naming }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->code1 }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->code2 }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->code3 }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->quality }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->price_sqm }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->price_kg }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->price_gram }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->weight_gr }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->length_m }}</td>
                                    <td class="px-3 py-2 border border-slate-200">{{ $row->weight_per_meter_gr }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200 text-center" colspan="19">
                                        Belum ada data validasi material.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">{{ $rows->links() }}</div>
            </section>
        </div>
    </div>

    <div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Import Validasi Material EJM</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <p class="mb-3 text-sm text-slate-600">Component yang didukung: <b>Bellow, Pipe - Nipple, Flange, Collar</b>.</p>
            <form method="POST" action="{{ route('setting.ejm-validation-material.import') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,.txt,.xlsx" required class="w-full rounded-lg border border-slate-300 p-2 text-sm">
                <div class="flex justify-end gap-2">
                    <button type="button" data-close-modal="importModal" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const importModal = document.getElementById('importModal');
            document.getElementById('openImportModal')?.addEventListener('click', function () {
                importModal?.classList.remove('hidden');
                importModal?.classList.add('flex');
            });
            document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const modal = document.getElementById(this.getAttribute('data-close-modal'));
                    modal?.classList.add('hidden');
                    modal?.classList.remove('flex');
                });
            });
        })();
    </script>
</x-app-layout>
