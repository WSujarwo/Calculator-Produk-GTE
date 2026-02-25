<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-start justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Validasi EJM Expansion Joint</h2>
                <p class="text-sm text-gray-600">Kelola tabel <code>validasi_dataejm_expansion_joint</code>.</p>
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
        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                {{ session('error') }}
            </div>
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
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold text-slate-900">EXPANSION JOINT METAL</h3>
                    <div class="flex gap-2">
                        @can('settings.ejm-validation.create')
                            <a href="{{ route('setting.ejm-expansion-joint.create') }}"
                               class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                + Add Data
                            </a>
                        @endcan
                        @can('settings.ejm-validation.export')
                            <a href="{{ route('setting.ejm-expansion-joint.template.csv') }}"
                               class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Template CSV
                            </a>
                            <a href="{{ route('setting.ejm-expansion-joint.template.excel') }}"
                               class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Template Excel
                            </a>
                        @endcan
                        @can('settings.ejm-validation.import')
                            <button type="button" id="openImportModal"
                                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Import CSV/XLSX
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto max-h-[72vh]">
                    <table class="min-w-[2800px] text-sm border-collapse">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-100 text-slate-900">
                                <th rowspan="2" class="px-2 py-2 border border-slate-300">Aksi</th>

                                <th colspan="4" class="px-2 py-2 border border-slate-300 text-center">SIZE</th>

                                {{-- EXPANSION JOINT METAL: 25 kolom --}}
                                <th colspan="25" class="px-2 py-2 border border-slate-300 text-center">EXPANSION JOINT METAL</th>

                                {{-- CIRCUMFERENCE: 3 kolom --}}
                                <th colspan="3" class="px-2 py-2 border border-slate-300 text-center">CIRCUMFERENCE</th>

                                {{-- CAN LENGTH: 1 kolom --}}
                                <th colspan="1" class="px-2 py-2 border border-slate-300 text-center">CAN LENGTH</th>

                                {{-- CIRCUMFERENCE COLLAR: 1 kolom --}}
                                <th colspan="1" class="px-2 py-2 border border-slate-300 text-center">CIRCUMFERENCE COLLAR</th>
                            </tr>

                            <tr class="bg-amber-100 text-slate-900">
                                {{-- SIZE --}}
                                <th class="px-2 py-2 border border-slate-300">INCH</th>
                                <th class="px-2 py-2 border border-slate-300">NB</th>
                                <th class="px-2 py-2 border border-slate-300">WIDTH</th>
                                <th class="px-2 py-2 border border-slate-300">LENGTH</th>

                                {{-- EXPANSION JOINT METAL (25) --}}
                                <th class="px-2 py-2 border border-slate-300">ID</th>
                                <th class="px-2 py-2 border border-slate-300">OD</th>
                                <th class="px-2 py-2 border border-slate-300">THK</th>
                                <th class="px-2 py-2 border border-slate-300">LY</th>
                                <th class="px-2 py-2 border border-slate-300">NOC (Default)</th>
                                <th class="px-2 py-2 border border-slate-300">LC</th>
                                <th class="px-2 py-2 border border-slate-300">TC</th>
                                <th class="px-2 py-2 border border-slate-300">P</th>
                                <th class="px-2 py-2 border border-slate-300">TR</th>
                                <th class="px-2 py-2 border border-slate-300">R</th>
                                <th class="px-2 py-2 border border-slate-300">OAL_B</th>
                                <th class="px-2 py-2 border border-slate-300">BL</th>
                                <th class="px-2 py-2 border border-slate-300">TL</th>
                                <th class="px-2 py-2 border border-slate-300">SLC</th>
                                <th class="px-2 py-2 border border-slate-300">LPE</th>
                                <th class="px-2 py-2 border border-slate-300">PRESS_MPA</th>
                                <th class="px-2 py-2 border border-slate-300">TEMP_C</th>
                                <th class="px-2 py-2 border border-slate-300">AXIAL_MM</th>
                                <th class="px-2 py-2 border border-slate-300">LSR_N_PER_MM</th>
                                <th class="px-2 py-2 border border-slate-300">MP_CI_MPA</th>
                                <th class="px-2 py-2 border border-slate-300">MP_II_MPA</th>
                                <th class="px-2 py-2 border border-slate-300">MLC</th>
                                <th class="px-2 py-2 border border-slate-300">GPF</th>
                                <th class="px-2 py-2 border border-slate-300">OAL</th>
                                <th class="px-2 py-2 border border-slate-300">AL</th>

                                {{-- CIRCUMFERENCE --}}
                                <th class="px-2 py-2 border border-slate-300">WIDTH1</th>
                                <th class="px-2 py-2 border border-slate-300">WIDTH2</th>
                                <th class="px-2 py-2 border border-slate-300">SPARE</th>

                                {{-- CAN LENGTH --}}
                                <th class="px-2 py-2 border border-slate-300">mm</th>

                                {{-- CIRCUMFERENCE COLLAR --}}
                                <th class="px-2 py-2 border border-slate-300">mm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-2 py-2 border border-slate-200">
                                        @can('settings.ejm-validation.edit')
                                            <a href="{{ route('setting.ejm-expansion-joint.index', ['edit' => $row->id]) }}"
                                               class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                    <td class="px-2 py-2 border border-slate-200">{{ $row->inch }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->nb }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->width }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->length }}</td>

                                        <td class="px-2 py-2 border border-slate-200">{{ $row->id_mm }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->od_mm }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->thk }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->ly }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->noc }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->lc }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->tc }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->p }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->tr }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->r }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->oal_b }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->bl }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->tl }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->slc }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->lpe }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->pres }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->temp_c }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->axial_m }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->lsr_n_per }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->mp_ci_mpa }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->mp_ii_mpa }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->mlc }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->gpf }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->oal }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->al }}</td>

                                        <td class="px-2 py-2 border border-slate-200">{{ $row->width1 }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->width2 }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->spare }}</td>

                                        <td class="px-2 py-2 border border-slate-200">{{ $row->can_length }}</td>
                                        <td class="px-2 py-2 border border-slate-200">{{ $row->circumference_collar }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200" colspan="36">
                                        Belum ada data expansion joint.
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

    @can('settings.ejm-validation.create')
    <div id="createModal" class="fixed inset-0 z-50 {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-6xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Expansion Joint</h3>
                <a href="{{ route('setting.ejm-expansion-joint.index') }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-expansion-joint.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                @csrf
                @foreach ($fields as $field)
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">{{ $field }}</span>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $field === 'shape_code' ? 'RND' : '') }}"
                               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                @endforeach
                <label class="text-xs text-slate-600">
                    <span class="font-semibold">is_active</span>
                    <select name="is_active" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" @selected(old('is_active', '1') === '1')>1</option>
                        <option value="0" @selected(old('is_active') === '0')>0</option>
                    </select>
                </label>
                <div class="md:col-span-5 flex justify-end gap-2 pt-2">
                    <a href="{{ route('setting.ejm-expansion-joint.index') }}"
                       class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</a>
                    <button type="submit"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('settings.ejm-validation.edit')
    <div id="editModal" class="fixed inset-0 z-50 {{ $editing ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-6xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Edit Expansion Joint</h3>
                <a href="{{ route('setting.ejm-expansion-joint.index') }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            @if ($editing)
                <form method="POST" action="{{ route('setting.ejm-expansion-joint.update') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editing['id'] }}">
                    @foreach ($fields as $field)
                        <label class="text-xs text-slate-600">
                            <span class="font-semibold">{{ $field }}</span>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $editing[$field] ?? '') }}"
                                   class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </label>
                    @endforeach
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">is_active</span>
                        <select name="is_active" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" @selected((string) old('is_active', (string) ($editing['is_active'] ?? '1')) === '1')>1</option>
                            <option value="0" @selected((string) old('is_active', (string) ($editing['is_active'] ?? '1')) === '0')>0</option>
                        </select>
                    </label>
                    <div class="md:col-span-5 flex justify-end gap-2 pt-2">
                        <a href="{{ route('setting.ejm-expansion-joint.index') }}"
                           class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</a>
                        <button type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endcan

    @can('settings.ejm-validation.import')
    <div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Import Expansion Joint (CSV/XLSX)</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <p class="mb-3 text-sm text-slate-600">Key update import: kombinasi <b>shape_code + size_code</b> (fallback `RND_NB{NB}`).</p>
            <div class="mb-3 flex gap-2">
                <a href="{{ route('setting.ejm-expansion-joint.template.csv') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                    Download CSV Template
                </a>
                <a href="{{ route('setting.ejm-expansion-joint.template.excel') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                    Download Excel Template
                </a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-expansion-joint.import') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,.txt,.xlsx" required
                       class="w-full rounded-lg border border-slate-300 p-2 text-sm">
                <div class="flex justify-end gap-2">
                    <button type="button" data-close-modal="importModal"
                            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
        (function () {
            const importModal = document.getElementById('importModal');
            const openImportBtn = document.getElementById('openImportModal');
            if (openImportBtn && importModal) {
                openImportBtn.addEventListener('click', function () {
                    importModal.classList.remove('hidden');
                    importModal.classList.add('flex');
                });
            }
            document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-close-modal');
                    const modal = document.getElementById(id);
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            });
        })();
    </script>
</x-app-layout>
