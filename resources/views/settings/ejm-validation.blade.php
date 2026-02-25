<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-start justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Validasi Data EJM</h2>
                <p class="text-sm text-gray-600">Kelola tabel <code>validasi_dataejm_can_length_calculations</code>.</p>
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
                    <h3 class="font-semibold text-slate-900">
                        {{ $activeTab === 'actual' ? 'ACTUAL DETAIL CALCULATION' : 'CALCULATION OF CAN LENGTH' }}
                    </h3>
                    <div class="flex gap-2">
                        @can('settings.ejm-validation.create')
                            <a href="{{ route('setting.ejm-validation.create', ['tab' => $activeTab]) }}"
                               class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                + Add Data
                            </a>
                        @endcan
                        @can('settings.ejm-validation.export')
                            <a href="{{ route('setting.ejm-validation.template.csv') }}"
                            class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Template CSV
                            </a>
                            <a href="{{ route('setting.ejm-validation.template.excel') }}"
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
                    <table class="min-w-[1500px] text-sm border-collapse">
                        <thead class="sticky top-0 z-10">
                            @if ($activeTab === 'actual')
                                <tr class="bg-emerald-100 text-slate-900">
                                    <th rowspan="2" class="px-3 py-2 border border-slate-300">Aksi</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-300">Size</th>
                                    <th colspan="3" class="px-3 py-2 border border-slate-300">TL</th>
                                    <th colspan="3" class="px-3 py-2 border border-slate-300">Spacer</th>
                                    <th colspan="4" class="px-3 py-2 border border-slate-300">Tool Radius</th>
                                    <th rowspan="2" class="px-3 py-2 border border-slate-300">TL + Spacer + Tool Radius</th>
                                    <th rowspan="2" class="px-3 py-2 border border-slate-300">GAP</th>
                                    <th rowspan="2" class="px-3 py-2 border border-slate-300">CAN Length</th>
                                </tr>
                                <tr class="bg-amber-100 text-slate-900">
                                    <th class="px-3 py-2 border border-slate-300">Inche</th>
                                    <th class="px-3 py-2 border border-slate-300">NB</th>
                                    <th class="px-3 py-2 border border-slate-300">Width</th>
                                    <th class="px-3 py-2 border border-slate-300">Qty</th>
                                    <th class="px-3 py-2 border border-slate-300">Total</th>
                                    <th class="px-3 py-2 border border-slate-300">Width</th>
                                    <th class="px-3 py-2 border border-slate-300">Qty</th>
                                    <th class="px-3 py-2 border border-slate-300">Total</th>
                                    <th class="px-3 py-2 border border-slate-300">Pitch EJMA</th>
                                    <th class="px-3 py-2 border border-slate-300">Pitch GTE</th>
                                    <th class="px-3 py-2 border border-slate-300">Qty</th>
                                    <th class="px-3 py-2 border border-slate-300">Total</th>
                                </tr>
                            @else
                                <tr class="bg-emerald-100 text-slate-900">
                                    <th rowspan="2" class="px-3 py-2 border border-slate-300">Aksi</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-300">Size</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-300">Bellows</th>
                                    <th colspan="4" class="px-3 py-2 border border-slate-300">EJMA Calculation</th>
                                    <th colspan="3" class="px-3 py-2 border border-slate-300">Manual Circumference</th>
                                    <th colspan="4" class="px-3 py-2 border border-slate-300">Current Correction</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-300">CAN Length</th>
                                </tr>
                                <tr class="bg-amber-100 text-slate-900">
                                    <th class="px-3 py-2 border border-slate-300">Inch</th>
                                    <th class="px-3 py-2 border border-slate-300">NB</th>
                                    <th class="px-3 py-2 border border-slate-300">ID</th>
                                    <th class="px-3 py-2 border border-slate-300">THK</th>
                                    <th class="px-3 py-2 border border-slate-300">LY</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-1</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-2</th>
                                    <th class="px-3 py-2 border border-slate-300">GAP</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-1</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-2</th>
                                    <th class="px-3 py-2 border border-slate-300">GAP</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-1</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm-2</th>
                                    <th class="px-3 py-2 border border-slate-300">GAP</th>
                                    <th class="px-3 py-2 border border-slate-300">Circm - 2 Actual</th>
                                    <th class="px-3 py-2 border border-slate-300">Calculation TL</th>
                                    <th class="px-3 py-2 border border-slate-300">Actual</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 border border-slate-200 whitespace-nowrap">
                                        @can('settings.ejm-validation.edit')
                                            <a href="{{ route('setting.ejm-validation.index', ['edit' => $row->id, 'tab' => $activeTab]) }}"
                                               class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                    @if ($activeTab === 'actual')
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->size_inch }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->nb }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tl_width }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tl_qty }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tl_total }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->spacer_width }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->spacer_qty }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->spacer_total }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->pitch_ejma }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->pitch_gte }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tool_radius_qty }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tool_radius_total }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->tl_spacer_tool_total }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->gap }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->can_length }}</td>
                                    @else
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->size_inch }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->nb }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->id_bellows ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->thk ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->ly ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->ejma_circm_1 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->ejma_circm_2 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->ejma_gap ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->manual_circm_1 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->manual_circm_2 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->manual_gap ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->correction_circm_1 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->correction_circm_2 ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->correction_gap ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->correction_circm_2_actual ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->calculation_tl ?? '-' }}</td>
                                        <td class="px-3 py-2 border border-slate-200">{{ $row->can_length_actual ?? $row->can_length ?? '-' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200" colspan="20">
                                        Belum ada data validasi EJM.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $rows->appends(['tab' => $activeTab])->links() }}
                </div>
            </section>
        </div>
    </div>

    @can('settings.ejm-validation.create')
    <div id="createModal" class="fixed inset-0 z-50 {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Data EJM</h3>
                <a href="{{ route('setting.ejm-validation.index', ['tab' => $activeTab]) }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-validation.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                @foreach ([
                    ['product_id', 1], ['shape_id', 1], ['product_shapes_id', 1], ['size_inch', null], ['nb', null],
                    ['tl_width', null], ['tl_qty', null], ['tl_total', null], ['spacer_width', null], ['spacer_qty', null],
                    ['spacer_total', null], ['pitch_ejma', null], ['pitch_gte', null], ['tool_radius_qty', null], ['tool_radius_total', null],
                    ['tl_spacer_tool_total', null], ['gap', 10], ['can_length', null],
                    ['id_bellows', null], ['thk', null], ['ly', null], ['ejma_circm_1', null], ['ejma_circm_2', null], ['ejma_gap', null],
                    ['manual_circm_1', null], ['manual_circm_2', null], ['manual_gap', null],
                    ['correction_circm_1', null], ['correction_circm_2', null], ['correction_gap', null], ['correction_circm_2_actual', null],
                    ['calculation_tl', null], ['can_length_actual', null], ['notes', null]
                ] as [$field, $default])
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">{{ $field }}</span>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $default) }}"
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
                <div class="md:col-span-4 flex justify-end gap-2 pt-2">
                    <a href="{{ route('setting.ejm-validation.index', ['tab' => $activeTab]) }}"
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
        <div class="w-full max-w-5xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Edit Data EJM</h3>
                <a href="{{ route('setting.ejm-validation.index', ['tab' => $activeTab]) }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            @if ($editing)
                <form method="POST" action="{{ route('setting.ejm-validation.update') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editing['id'] }}">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    @foreach ([
                        'product_id','shape_id','product_shapes_id','size_inch','nb',
                        'tl_width','tl_qty','tl_total','spacer_width','spacer_qty','spacer_total','pitch_ejma','pitch_gte','tool_radius_qty','tool_radius_total','tl_spacer_tool_total','gap','can_length',
                        'id_bellows','thk','ly','ejma_circm_1','ejma_circm_2','ejma_gap',
                        'manual_circm_1','manual_circm_2','manual_gap',
                        'correction_circm_1','correction_circm_2','correction_gap','correction_circm_2_actual','calculation_tl','can_length_actual','notes'
                    ] as $field)
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
                    <div class="md:col-span-4 flex justify-end gap-2 pt-2">
                        <a href="{{ route('setting.ejm-validation.index', ['tab' => $activeTab]) }}"
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
                <h3 class="text-lg font-semibold text-slate-900">Import EJM (CSV/XLSX)</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <p class="mb-3 text-sm text-slate-600">Identifier import adalah kolom <b>NB</b>. NB sama akan update, NB baru akan insert.</p>
            <div class="mb-3 flex gap-2">
                <a href="{{ route('setting.ejm-validation.template.csv') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">Download CSV Template</a>
                <a href="{{ route('setting.ejm-validation.template.excel') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">Download Excel Template</a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-validation.import') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="tab" value="{{ $activeTab }}">
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
