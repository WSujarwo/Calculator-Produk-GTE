<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-start justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Validasi Proses EJM</h2>
                <p class="text-sm text-gray-600">Kelola tabel <code>data_validasiejm_proses</code>.</p>
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
            <div class="border-b border-slate-200 px-4 py-3">
                <form method="GET" action="{{ route('setting.ejm-validation-proses.index') }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Component Type</span>
                        <input type="text" name="component_type" value="{{ request('component_type') }}"
                               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">Process Name</span>
                        <input type="text" name="process_name" value="{{ request('process_name') }}"
                               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">NB</span>
                        <input type="number" name="nb" value="{{ request('nb') }}"
                               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Filter
                        </button>
                        <a href="{{ route('setting.ejm-validation-proses.index') }}"
                           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <h3 class="font-semibold text-slate-900">Data Validasi Proses</h3>
                <div class="flex flex-wrap gap-2">
                    @can('settings.ejm-validation.create')
                        <a href="{{ route('setting.ejm-validation-proses.create') }}"
                           class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            + Add Data
                        </a>
                    @endcan
                    @can('settings.ejm-validation.export')
                        <a href="{{ route('setting.ejm-validation-proses.template.csv') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Template CSV
                        </a>
                        <a href="{{ route('setting.ejm-validation-proses.template.excel') }}"
                           class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Template Excel
                        </a>
                        <a href="{{ route('setting.ejm-validation-proses.export.csv', request()->query()) }}"
                           class="inline-flex items-center rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                            Export CSV
                        </a>
                        <a href="{{ route('setting.ejm-validation-proses.export.excel', request()->query()) }}"
                           class="inline-flex items-center rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                            Export Excel
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

            <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                <table class="min-w-[1200px] text-sm border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-emerald-100 text-slate-900">
                            <th class="px-3 py-2 border border-slate-300">Aksi</th>
                            <th class="px-3 py-2 border border-slate-300">Component Type</th>
                            <th class="px-3 py-2 border border-slate-300">Process Name</th>
                            <th class="px-3 py-2 border border-slate-300">NB</th>
                            <th class="px-3 py-2 border border-slate-300">Tube Inner</th>
                            <th class="px-3 py-2 border border-slate-300">Price Tube Inner</th>
                            <th class="px-3 py-2 border border-slate-300">Tube Outer</th>
                            <th class="px-3 py-2 border border-slate-300">Price Tube Outer</th>
                            <th class="px-3 py-2 border border-slate-300">Unit</th>
                            <th class="px-3 py-2 border border-slate-300">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 border border-slate-200 whitespace-nowrap">
                                    @can('settings.ejm-validation.edit')
                                        <a href="{{ route('setting.ejm-validation-proses.index', array_merge(request()->query(), ['edit' => $row->id])) }}"
                                           class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Edit
                                        </a>
                                    @endcan
                                    @can('settings.ejm-validation.delete')
                                        <form method="POST" action="{{ route('setting.ejm-validation-proses.delete') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $row->id }}">
                                            <button type="submit"
                                                    onclick="return confirm('Yakin hapus data ini?')"
                                                    class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->component_type }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->process_name }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->nb }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tube_inner }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->price_tube_inner }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->tube_outer }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->price_tube_outer }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->unit }}</td>
                                <td class="px-3 py-2 border border-slate-200">{{ $row->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-6 text-sm text-slate-500 border border-slate-200 text-center" colspan="10">
                                    Belum ada data validasi proses.
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
        <div class="w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Validasi Proses</h3>
                <a href="{{ route('setting.ejm-validation-proses.index') }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-validation-proses.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf
                @foreach ([
                    ['component_type', null],
                    ['process_name', null],
                    ['nb', null],
                    ['tube_inner', null],
                    ['price_tube_inner', null],
                    ['tube_outer', null],
                    ['price_tube_outer', null],
                    ['unit', 'menit'],
                    ['notes', null],
                ] as [$field, $default])
                    <label class="text-xs text-slate-600">
                        <span class="font-semibold">{{ $field }}</span>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $default) }}"
                               class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                @endforeach
                <div class="md:col-span-3 flex justify-end gap-2 pt-2">
                    <a href="{{ route('setting.ejm-validation-proses.index') }}"
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
        <div class="w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Edit Validasi Proses</h3>
                <a href="{{ route('setting.ejm-validation-proses.index', request()->query()) }}" class="text-slate-500 hover:text-slate-700">x</a>
            </div>
            @if ($editing)
                <form method="POST" action="{{ route('setting.ejm-validation-proses.update') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editing['id'] }}">
                    @foreach ([
                        'component_type',
                        'process_name',
                        'nb',
                        'tube_inner',
                        'price_tube_inner',
                        'tube_outer',
                        'price_tube_outer',
                        'unit',
                        'notes',
                    ] as $field)
                        <label class="text-xs text-slate-600">
                            <span class="font-semibold">{{ $field }}</span>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $editing[$field] ?? '') }}"
                                   class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </label>
                    @endforeach
                    <div class="md:col-span-3 flex justify-end gap-2 pt-2">
                        <a href="{{ route('setting.ejm-validation-proses.index', request()->query()) }}"
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
                <h3 class="text-lg font-semibold text-slate-900">Import Validasi Proses</h3>
                <button type="button" data-close-modal="importModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <p class="mb-3 text-sm text-slate-600">Key update import: kombinasi <b>component_type + process_name + nb</b>.</p>
            <div class="mb-3 flex gap-2">
                <a href="{{ route('setting.ejm-validation-proses.template.csv') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                    Download CSV Template
                </a>
                <a href="{{ route('setting.ejm-validation-proses.template.excel') }}"
                   class="inline-flex items-center rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                    Download Excel Template
                </a>
            </div>
            <form method="POST" action="{{ route('setting.ejm-validation-proses.import') }}" enctype="multipart/form-data" class="space-y-3">
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
