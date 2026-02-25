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
                    <h3 class="font-semibold text-slate-900">Data Expansion Joint</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('setting.ejm-expansion-joint.create') }}"
                           class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            + Add Data
                        </a>
                        <button type="button" id="openImportModal"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Import CSV/XLSX
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                    <table class="min-w-[1800px] text-sm border-collapse">
                    <thead class="sticky top-0 z-10 bg-slate-100">
                        <tr class="text-slate-900">
                            <th class="px-3 py-2 border border-slate-300">Aksi</th>
                            <th class="px-3 py-2 border border-slate-300">Shape</th>
                            <th class="px-3 py-2 border border-slate-300">Size Code</th>
                            <th class="px-3 py-2 border border-slate-300">NB</th>
                            <th class="px-3 py-2 border border-slate-300">Width</th>
                            <th class="px-3 py-2 border border-slate-300">Length</th>
                            <th class="px-3 py-2 border border-slate-300">TL/Side</th>
                            <th class="px-3 py-2 border border-slate-300">TL Qty</th>
                            <th class="px-3 py-2 border border-slate-300">Spacer Width</th>
                            <th class="px-3 py-2 border border-slate-300">Spacer Qty</th>
                            <th class="px-3 py-2 border border-slate-300">Tool Radius</th>
                            <th class="px-3 py-2 border border-slate-300">Tool Radius Qty</th>
                            <th class="px-3 py-2 border border-slate-300">Pitch EJMA</th>
                            <th class="px-3 py-2 border border-slate-300">Pitch GTE</th>
                            <th class="px-3 py-2 border border-slate-300">Total TL</th>
                            <th class="px-3 py-2 border border-slate-300">Total Spacer</th>
                            <th class="px-3 py-2 border border-slate-300">Total Tool Radius</th>
                            <th class="px-3 py-2 border border-slate-300">TL+Spacer+Tool</th>
                            <th class="px-3 py-2 border border-slate-300">Gap</th>
                            <th class="px-3 py-2 border border-slate-300">Can Length</th>
                            <th class="px-3 py-2 border border-slate-300">Effective From</th>
                            <th class="px-3 py-2 border border-slate-300">Effective To</th>
                            <th class="px-3 py-2 border border-slate-300">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 border border-slate-200">
                                    <a href="{{ route('setting.ejm-expansion-joint.index', ['edit' => $row->id]) }}"
                                       class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        Edit
                                    </a>
                                </td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->shape_code }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->size_code ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->nb ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->width_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->length_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tl_per_side_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tl_qty ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->spacer_width_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->spacer_qty ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tool_radius_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tool_radius_qty ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->pitch_ejma_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->pitch_gte_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->total_tl_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->total_spacer_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->total_tool_radius_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tl_spacer_tool_total_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->gap_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->can_length_mm ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->effective_from ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->effective_to ?? '-' }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->is_active ? 'Yes' : 'No' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200" colspan="23">
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

    <div id="createModal" class="fixed inset-0 z-50 {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-6xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Expansion Joint</h3>
                <a href="{{ route('setting.ejm-expansion-joint.index') }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-expansion-joint.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                @php
                    $fields = [
                        'standard_version_id','shape_code','size_code','nb','width_mm','length_mm',
                        'tl_per_side_mm','tl_qty','spacer_width_mm','spacer_qty','tool_radius_mm','tool_radius_qty',
                        'pitch_ejma_mm','pitch_gte_mm','total_tl_mm','total_spacer_mm','total_tool_radius_mm',
                        'tl_spacer_tool_total_mm','gap_mm','can_length_mm','effective_from','effective_to','notes'
                    ];
                @endphp
                @foreach ($fields as $field)
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">{{ $field }}</span>
                        <input type="{{ in_array($field, ['effective_from', 'effective_to']) ? 'date' : 'text' }}"
                               name="{{ $field }}" value="{{ old($field, $field === 'shape_code' ? 'RND' : '') }}"
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
                    <a href="{{ route('setting.ejm-expansion-joint.index') }}"
                       class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</a>
                    <button type="submit"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 {{ $editing ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-6xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Edit Expansion Joint</h3>
                <a href="{{ route('setting.ejm-expansion-joint.index') }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            @if ($editing)
                <form method="POST" action="{{ route('setting.ejm-expansion-joint.update') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editing['id'] }}">
                    @foreach ($fields as $field)
                        <label class="text-xs text-slate-600">
                            <span class="font-semibold">{{ $field }}</span>
                            <input type="{{ in_array($field, ['effective_from', 'effective_to']) ? 'date' : 'text' }}"
                                   name="{{ $field }}" value="{{ old($field, $editing[$field] ?? '') }}"
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
                        <a href="{{ route('setting.ejm-expansion-joint.index') }}"
                           class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</a>
                        <button type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Import Expansion Joint (CSV/XLSX)</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <p class="mb-3 text-sm text-slate-600">Key update import: kombinasi <b>shape_code + size_code</b>.</p>
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
