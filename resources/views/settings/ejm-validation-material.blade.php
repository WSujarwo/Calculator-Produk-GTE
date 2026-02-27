{{-- resources/views/settings/ejm-validation-material.blade.php --}}
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

    @php
        /**
         * Formatter UI (tampilan enak dibaca)
         */
        $isEmpty = function ($v) {
            return $v === null || $v === '' || (is_string($v) && trim($v) === '');
        };

        $fmtInt = function ($v) use ($isEmpty) {
            if ($isEmpty($v)) return '';
            return number_format((float) $v, 0, ',', '.');
        };

        $fmtDecTrim = function ($v, $maxDec = 6) use ($isEmpty) {
            if ($isEmpty($v)) return '';
            $s = number_format((float) $v, (int) $maxDec, '.', '');
            $s = rtrim(rtrim($s, '0'), '.');

            if (str_contains($s, '.')) {
                [$a, $b] = explode('.', $s, 2);
                $a = number_format((float) $a, 0, ',', '.');
                return $a . ',' . $b;
            }

            return number_format((float) $s, 0, ',', '.');
        };

        $rupiah = function ($v) use ($isEmpty, $fmtInt) {
            if ($isEmpty($v)) return '';
            return 'Rp ' . $fmtInt($v);
        };

        // untuk ngisi input type="text" (jangan pakai format ribuan)
        $val = function ($v) use ($isEmpty) {
            return $isEmpty($v) ? '' : (string) $v;
        };

        $components = ['Bellow', 'Pipe - Nipple', 'Flange', 'Collar'];
    @endphp

    <div class="w-full px-6 lg:px-10 py-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- validation errors (optional) --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <div class="font-semibold mb-1">Validation Error</div>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            {{-- SIDEBAR --}}
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

            {{-- CONTENT --}}
            <section class="xl:col-span-9 rounded-2xl border border-slate-200 bg-white shadow-sm">
                {{-- FILTER --}}
                <div class="border-b border-slate-200 px-4 py-3">
                    <form method="GET"
                          action="{{ route('setting.ejm-validation-material.index') }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-3">

                        <label class="text-xs text-slate-600">
                            <span class="font-semibold">Component</span>
                            <select name="component" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                <option value="">-- All --</option>
                                @foreach ($components as $component)
                                    <option value="{{ $component }}" @selected(request('component') === $component)>
                                        {{ $component }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="text-xs text-slate-600 md:col-span-2">
                            <span class="font-semibold">Search</span>
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   class="mt-1 w-full rounded-lg border-slate-300 text-sm"
                                   placeholder="material / part number / naming">
                        </label>

                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Filter
                            </button>
                            <a href="{{ route('setting.ejm-validation-material.index') }}"
                               class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold text-slate-900">Data Validasi Material</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('setting.ejm-validation-material.template.csv') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Template CSV
                        </a>
                        <a href="{{ route('setting.ejm-validation-material.template.excel') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Template Excel
                        </a>
                        <button type="button"
                                id="openImportModal"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Import CSV/XLSX
                        </button>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                    <table class="min-w-[1350px] text-sm border-collapse">
                        <thead class="sticky top-0 z-10">
                        <tr class="bg-emerald-100 text-slate-900">
                            <th class="px-3 py-2 border border-slate-300">Component</th>
                            <th class="px-3 py-2 border border-slate-300">Material</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Thk</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Ply</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Size</th>
                            <th class="px-3 py-2 border border-slate-300">Sch</th>
                            <th class="px-3 py-2 border border-slate-300">Type</th>
                            <th class="px-3 py-2 border border-slate-300">Part Number</th>
                            <th class="px-3 py-2 border border-slate-300">Naming</th>
                            <th class="px-3 py-2 border border-slate-300">Code1</th>
                            <th class="px-3 py-2 border border-slate-300">Code2</th>
                            <th class="px-3 py-2 border border-slate-300">Code3</th>
                            <th class="px-3 py-2 border border-slate-300">Quality</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Price SQM</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Price KG</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Price Gram</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Berat (gr)</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Panjang (m)</th>
                            <th class="px-3 py-2 border border-slate-300 text-right">Berat/m (gr)</th>
                            <th class="px-3 py-2 border border-slate-300 text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($rows as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 border border-slate-200">{{ $row->component }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->material }}</td>

                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->thk_mm, 3) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->ply, 0) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->size_in, 3) }}</td>

                                <td class="px-3 py-2 border border-slate-200">{{ $row->sch }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->type }}</td>
                                <td class="px-3 py-2 border border-slate-200 font-mono text-xs">{{ $row->part_number }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->naming }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->code1 }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->code2 }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->code3 }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->quality }}</td>

                                <td class="px-3 py-2 border border-slate-200 text-right whitespace-nowrap">{{ $rupiah($row->price_sqm) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right whitespace-nowrap">{{ $rupiah($row->price_kg) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->price_gram, 6) }}</td>

                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->weight_gr, 4) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->length_m, 6) }}</td>
                                <td class="px-3 py-2 border border-slate-200 text-right">{{ $fmtDecTrim($row->weight_per_meter_gr, 4) }}</td>

                                {{-- ACTIONS --}}
                                <td class="px-3 py-2 border border-slate-200 text-center whitespace-nowrap">
                                    <button type="button"
                                            class="inline-flex items-center rounded-lg border border-indigo-300 bg-white px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50"
                                            data-edit-row='@json($row)'>
                                        Edit
                                    </button>

                                    <button type="button"
                                            class="inline-flex items-center rounded-lg border border-rose-300 bg-white px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                            data-delete-id="{{ $row->id }}"
                                            data-delete-name="{{ $row->part_number }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200 text-center" colspan="20">
                                    Belum ada data validasi material.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $rows->links() }}
                </div>
            </section>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Import Validasi Material EJM</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <p class="mb-3 text-sm text-slate-600">
                Component yang didukung: <b>Bellow, Pipe - Nipple, Flange, Collar</b>.
            </p>

            <form method="POST"
                  action="{{ route('setting.ejm-validation-material.import') }}"
                  enctype="multipart/form-data"
                  class="space-y-3">
                @csrf
                <input type="file"
                       name="file"
                       accept=".csv,.txt,.xlsx"
                       required
                       class="w-full rounded-lg border border-slate-300 p-2 text-sm">
                <div class="flex justify-end gap-2">
                    <button type="button"
                            data-close-modal="importModal"
                            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Edit Validasi Material</h3>
                    <p class="text-xs text-slate-600">Edit data record (ejm_special_materials).</p>
                </div>
                <button type="button" data-close-modal="editModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <form id="editForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Component</span>
                        <select name="component" id="edit_component" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            @foreach ($components as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="text-xs text-slate-600 md:col-span-2">
                        <span class="font-semibold">Material</span>
                        <input type="text" name="material" id="edit_material" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Thk (mm)</span>
                        <input type="text" name="thk_mm" id="edit_thk_mm" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Ply</span>
                        <input type="text" name="ply" id="edit_ply" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Size (inch)</span>
                        <input type="text" name="size_in" id="edit_size_in" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Sch</span>
                        <input type="text" name="sch" id="edit_sch" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Type</span>
                        <input type="text" name="type" id="edit_type" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600 md:col-span-2">
                        <span class="font-semibold">Part Number</span>
                        <input type="text" name="part_number" id="edit_part_number" class="mt-1 w-full rounded-lg border-slate-300 text-sm" readonly>
                        <p class="mt-1 text-[11px] text-slate-500">Part Number biasanya jadi key; untuk keamanan dibuat read-only.</p>
                    </label>

                    <label class="text-xs text-slate-600 md:col-span-3">
                        <span class="font-semibold">Naming</span>
                        <input type="text" name="naming" id="edit_naming" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Code1</span>
                        <input type="text" name="code1" id="edit_code1" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Code2</span>
                        <input type="text" name="code2" id="edit_code2" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Code3</span>
                        <input type="text" name="code3" id="edit_code3" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Quality</span>
                        <input type="text" name="quality" id="edit_quality" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Price SQM</span>
                        <input type="text" name="price_sqm" id="edit_price_sqm" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Price KG</span>
                        <input type="text" name="price_kg" id="edit_price_kg" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Price Gram</span>
                        <input type="text" name="price_gram" id="edit_price_gram" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Berat (gr)</span>
                        <input type="text" name="weight_gr" id="edit_weight_gr" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Panjang (m)</span>
                        <input type="text" name="length_m" id="edit_length_m" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>

                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Berat/m (gr)</span>
                        <input type="text" name="weight_per_meter_gr" id="edit_weight_per_meter_gr" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button"
                            data-close-modal="editModal"
                            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Delete Confirmation</h3>
                <button type="button" data-close-modal="deleteModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <p class="text-sm text-slate-700">
                Yakin mau hapus data: <b id="deleteName">-</b> ?
            </p>
            <p class="mt-1 text-xs text-rose-600">Aksi ini tidak bisa dibatalkan.</p>

            <form id="deleteForm" method="POST" action="#" class="mt-4 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button"
                        data-close-modal="deleteModal"
                        class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Cancel
                </button>
                <button type="submit"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const importModal = document.getElementById('importModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');

            // open import modal
            document.getElementById('openImportModal')?.addEventListener('click', function () {
                importModal?.classList.remove('hidden');
                importModal?.classList.add('flex');
            });

            // close modals
            document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const modal = document.getElementById(this.getAttribute('data-close-modal'));
                    modal?.classList.add('hidden');
                    modal?.classList.remove('flex');
                });
            });

            // helpers
            function openModal(modalEl) {
                modalEl?.classList.remove('hidden');
                modalEl?.classList.add('flex');
            }
            function setVal(id, value) {
                const el = document.getElementById(id);
                if (!el) return;
                el.value = (value === null || value === undefined) ? '' : String(value);
            }

            // EDIT
            const editForm = document.getElementById('editForm');

            document.querySelectorAll('[data-edit-row]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    let row;
                    try {
                        row = JSON.parse(this.getAttribute('data-edit-row') || '{}');
                    } catch (e) {
                        row = {};
                    }

                    if (!row.id) return;

                    // set action route
                    // NOTE: butuh route name: setting.ejm-validation-material.update
                    const action = @json(route('setting.ejm-validation-material.update', ['id' => '__ID__']));
                    editForm.action = action.replace('__ID__', row.id);

                    setVal('edit_component', row.component);
                    setVal('edit_material', row.material);
                    setVal('edit_thk_mm', row.thk_mm);
                    setVal('edit_ply', row.ply);
                    setVal('edit_size_in', row.size_in);
                    setVal('edit_sch', row.sch);
                    setVal('edit_type', row.type);
                    setVal('edit_part_number', row.part_number);
                    setVal('edit_naming', row.naming);
                    setVal('edit_code1', row.code1);
                    setVal('edit_code2', row.code2);
                    setVal('edit_code3', row.code3);
                    setVal('edit_quality', row.quality);

                    setVal('edit_price_sqm', row.price_sqm);
                    setVal('edit_price_kg', row.price_kg);
                    setVal('edit_price_gram', row.price_gram);

                    setVal('edit_weight_gr', row.weight_gr);
                    setVal('edit_length_m', row.length_m);
                    setVal('edit_weight_per_meter_gr', row.weight_per_meter_gr);

                    openModal(editModal);
                });
            });

            // DELETE
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('deleteName');

            document.querySelectorAll('[data-delete-id]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-delete-id');
                    const name = this.getAttribute('data-delete-name') || '-';
                    if (!id) return;

                    // NOTE: butuh route name: setting.ejm-validation-material.destroy
                    const action = @json(route('setting.ejm-validation-material.destroy', ['id' => '__ID__']));
                    deleteForm.action = action.replace('__ID__', id);
                    deleteName.textContent = name;

                    openModal(deleteModal);
                });
            });
        })();
    </script>
</x-app-layout>