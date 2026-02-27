<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Calculation EJM</h2>
            <p class="text-sm text-gray-300">PCE Header, PCE Item, dan Summary Product</p>
        </div>
    </x-slot>

    @php
        $selectedHeader = $selectedHeader ?? null;
        $selectedHeaderId = (int) ($selectedHeaderId ?? 0);
        $canInputItem = $selectedHeaderId > 0;
    @endphp

    <div class="w-full px-6 lg:px-10 py-6 space-y-6">
        <div id="ejm-alert" class="hidden rounded-lg border px-4 py-3 text-sm"></div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Active PCE Header</div>
            <div class="p-4 lg:p-5 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 items-end">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"><div class="text-xs text-slate-500">PCE Number</div><div class="text-sm font-semibold text-slate-900">{{ $selectedHeader?->pce_number ?? '-' }}</div></div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"><div class="text-xs text-slate-500">Project</div><div class="text-sm font-semibold text-slate-900">{{ $selectedHeader?->project_of_name ?? '-' }}</div></div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"><div class="text-xs text-slate-500">Total Item</div><div class="text-sm font-semibold text-slate-900">{{ $summaryRows->count() }}</div></div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="open-header-modal-btn" class="inline-flex h-10 items-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Cari / Buat PCE Header</button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">PCE Item Form - {{ $selectedHeader?->pce_number ?? 'Pilih Header Terlebih Dahulu' }}</div>
            <form id="pce-item-form" class="p-4 lg:p-5 space-y-4">
                @csrf
                <input type="hidden" id="item-id" value="">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div><label class="block text-sm font-semibold text-slate-700">Shape</label><select id="item-shape-id" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><option value="">-- Pilih Shape --</option>@foreach ($shapes as $shape)<option value="{{ $shape->id }}">{{ $shape->shape_code }} - {{ $shape->shape_name }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Type / Configuration</label><select id="item-type-config-id" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><option value="">-- Pilih Type --</option>@foreach ($types as $type)<option value="{{ $type->id }}" data-shape-id="{{ $type->shape_id }}">{{ $type->type_code }} - {{ $type->type_name }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Size (NB)</label><select id="item-size-nb" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }} required><option value="">-- Pilih NB --</option>@foreach ($sizeCatalog as $size)<option value="{{ $size->nb }}">{{ $size->nb }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Quantity</label><input id="item-quantity" type="number" min="1" value="1" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}></div>
                    <div><label class="block text-sm font-semibold text-slate-700">NOC (max validasi NB)</label><input id="item-noc" type="number" min="0" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><p id="item-noc-hint" class="mt-1 text-xs text-slate-500">Pilih NB dulu untuk membaca NOC maksimum.</p></div>
                    <div><label class="block text-sm font-semibold text-slate-700">ID (auto)</label><input id="item-id-mm" type="text" class="mt-1 w-full rounded-md border-slate-200 bg-slate-100 text-sm" readonly></div>
                    <div><label class="block text-sm font-semibold text-slate-700">OD (auto)</label><input id="item-od-mm" type="text" class="mt-1 w-full rounded-md border-slate-200 bg-slate-100 text-sm" readonly></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Thk (auto)</label><input id="item-thk-mm" type="text" class="mt-1 w-full rounded-md border-slate-200 bg-slate-100 text-sm" readonly></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Ply (auto)</label><input id="item-ply" type="text" class="mt-1 w-full rounded-md border-slate-200 bg-slate-100 text-sm" readonly></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Material Bellow</label><select id="item-material-bellow-id" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><option value="">-- Pilih Material Bellow --</option>@foreach ($bellowMaterials as $material)<option value="{{ $material->material_id }}">{{ $material->material_name }}{{ $material->thk_mm ? ' | Thk ' . $material->thk_mm : '' }}{{ $material->jumlah_ply ? ' | Ply ' . $material->jumlah_ply : '' }}{{ $material->price_sqm ? ' | Price SQM ' . number_format((float) $material->price_sqm, 0, '.', ',') : '' }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Material Flange</label><select id="item-material-flange-id" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><option value="">-- Pilih Material Flange --</option>@foreach ($flangeMaterials as $material)<option value="{{ $material->material_id }}">{{ $material->material_name }}{{ $material->size_in ? ' | Size ' . $material->size_in : '' }}{{ $material->type ? ' | ' . $material->type : '' }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Material Pipe End</label><select id="item-material-pipe-end-id" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}><option value="">-- Pilih Material Pipe Nipple --</option>@foreach ($pipeNippleMaterials as $material)<option value="{{ $material->material_id }}">{{ $material->material_name }}{{ $material->thk_mm ? ' | Thk ' . $material->thk_mm : '' }}{{ $material->size_in ? ' | Size ' . $material->size_in : '' }}{{ $material->type ? ' | ' . $material->type : '' }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-semibold text-slate-700">Status</label><select id="item-status" class="mt-1 w-full rounded-md border-slate-300 text-sm" {{ $canInputItem ? '' : 'disabled' }}>@foreach (['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'CANCELLED'] as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></div>
                </div>
                <div class="flex flex-wrap justify-end gap-2 border-t border-gray-200 pt-3"><button type="button" id="reset-item-btn" class="inline-flex h-10 items-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset Item</button><button type="submit" class="inline-flex h-10 items-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700" {{ $canInputItem ? '' : 'disabled' }}>Simpan Item</button></div>
            </form>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="bg-slate-800 border-b border-slate-700 px-4 py-2 text-center font-semibold text-white">Summary Item - {{ $selectedHeader?->pce_number ?? '-' }}</div>
            <div class="overflow-auto">
                <table class="w-full text-sm min-w-[1280px]">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left">No</th><th class="px-3 py-2 text-right">Qty</th><th class="px-3 py-2 text-left">Shape</th><th class="px-3 py-2 text-left">Type</th><th class="px-3 py-2 text-right">NB</th><th class="px-3 py-2 text-right">ID</th><th class="px-3 py-2 text-right">OD</th><th class="px-3 py-2 text-right">Thk</th><th class="px-3 py-2 text-right">Ply</th><th class="px-3 py-2 text-right">NOC</th><th class="px-3 py-2 text-left">Bellow</th><th class="px-3 py-2 text-left">Flange</th><th class="px-3 py-2 text-left">Pipe End</th><th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="item-summary-body" class="divide-y divide-slate-100">
                        @forelse ($summaryRows as $idx => $item)
                            <tr>
                                <td class="px-3 py-2">{{ $idx + 1 }}</td><td class="px-3 py-2 text-right font-semibold">{{ $item->quantity ?? 1 }}</td><td class="px-3 py-2">{{ $item->shape?->shape_name ?? '-' }}</td><td class="px-3 py-2">{{ $item->typeConfig?->type_name ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->size_nb ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->id_mm ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->od_mm ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->thk_mm ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->ply ?? '-' }}</td><td class="px-3 py-2 text-right">{{ $item->noc ?? '-' }}</td><td class="px-3 py-2">{{ $item->materialBellow?->part_number ?? '-' }}</td><td class="px-3 py-2">{{ $item->materialFlange?->part_number ?? '-' }}</td><td class="px-3 py-2">{{ $item->materialPipeEnd?->part_number ?? '-' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @php
                                        $editItemPayload = [
                                            'id' => $item->id,
                                            'shape_id' => $item->shape_id,
                                            'type_config_id' => $item->type_config_id,
                                            'size_nb' => $item->size_nb,
                                            'quantity' => $item->quantity,
                                            'noc' => $item->noc,
                                            'id_mm' => $item->id_mm,
                                            'od_mm' => $item->od_mm,
                                            'thk_mm' => $item->thk_mm,
                                            'ply' => $item->ply,
                                            'material_bellow_id' => $item->material_bellow_id,
                                            'material_flange_id' => $item->material_flange_id,
                                            'material_pipe_end_id' => $item->material_pipe_end_id,
                                            'status' => $item->status,
                                        ];
                                    @endphp
                                    <button type="button" class="inline-flex rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50" data-edit-item="{{ e(json_encode($editItemPayload)) }}">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="14" class="px-3 py-6 text-center text-slate-500">Belum ada item pada header ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="pce-header-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-6xl rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between bg-slate-800 px-4 py-3 text-white"><h3 class="text-sm font-semibold">PCE Header - Search, Select, Create</h3><button id="close-header-modal-btn" type="button" class="rounded border border-slate-500 px-2 py-1 text-xs hover:bg-slate-700">Tutup</button></div>
            <div class="grid grid-cols-1 xl:grid-cols-[1.25fr_1fr] gap-4 p-4">
                <div class="rounded-xl border border-slate-200">
                    <form method="GET" action="{{ route('calculation.ejm') }}" class="grid grid-cols-1 md:grid-cols-[1fr_180px_auto] gap-2 border-b border-slate-200 p-3">
                        <input type="text" name="header_search" value="{{ $headerSearch }}" placeholder="Cari PCE Number / Project" class="rounded-md border-slate-300 text-sm">
                        <input type="date" name="header_date" value="{{ $headerDate }}" class="rounded-md border-slate-300 text-sm">
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Search</button>
                    </form>
                    <div class="max-h-[400px] overflow-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 sticky top-0"><tr><th class="px-3 py-2 text-left">PCE Number</th><th class="px-3 py-2 text-left">Date</th><th class="px-3 py-2 text-center">Item</th><th class="px-3 py-2 text-center">Pilih</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($headers as $header)
                                    <tr class="{{ (int) $selectedHeaderId === (int) $header->id ? 'bg-indigo-50/70' : '' }}">
                                        <td class="px-3 py-2">{{ $header->pce_number }}</td><td class="px-3 py-2">{{ optional($header->date)->format('d-m-Y') ?: '-' }}</td><td class="px-3 py-2 text-center">{{ $header->items_count }}</td><td class="px-3 py-2 text-center"><a href="{{ route('calculation.ejm', ['header_id' => $header->id]) }}" class="inline-flex rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50">Pilih</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-3 py-6 text-center text-slate-500">Data header tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200">
                    <div class="border-b border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900">Create PCE Header</div>
                    <form id="pce-header-form" class="space-y-3 p-3">
                        @csrf
                        <input id="pce-number" type="text" placeholder="PCE Number" class="w-full rounded-md border-slate-300 text-sm" required>
                        <input id="project-of-name" type="text" value="Expansion Joint Metal" class="w-full rounded-md border-slate-300 text-sm">
                        <select id="end-user-id" class="w-full rounded-md border-slate-300 text-sm"><option value="">-- End User --</option>@foreach ($endUsers as $row)<option value="{{ $row->id }}">{{ $row->company_code }} - {{ $row->company_name }}</option>@endforeach</select>
                        <input id="area" type="text" placeholder="Area" class="w-full rounded-md border-slate-300 text-sm">
                        <input id="drawing-no" type="text" placeholder="Drawing No" class="w-full rounded-md border-slate-300 text-sm">
                        <input id="document-no" type="text" placeholder="Document No" class="w-full rounded-md border-slate-300 text-sm">
                        <input id="revision" type="text" placeholder="Revision" class="w-full rounded-md border-slate-300 text-sm">
                        <input id="header-date" type="date" class="w-full rounded-md border-slate-300 text-sm">
                        <select id="sales-id" class="w-full rounded-md border-slate-300 text-sm"><option value="">-- Sales --</option>@foreach ($salesUsers as $row)<option value="{{ $row->id }}">{{ $row->marketing_no }} - {{ $row->name }}</option>@endforeach</select>
                        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Create Header</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectedHeaderId = Number(@json($selectedHeaderId));
            const calcPageUrl = @json(route('calculation.ejm'));
            const createHeaderUrl = @json(route('calculation.ejm.pce.headers.store'));
            const createItemUrlTemplate = @json(route('calculation.ejm.pce.items.store', ['pceHeader' => '__ID__']));
            const updateItemUrlTemplate = @json(route('calculation.ejm.pce.items.update', ['pceHeader' => '__HID__', 'pceItem' => '__IID__']));
            const lookupUrl = @json(route('calculation.ejm.pce.lookup.validation'));

            const modal = document.getElementById('pce-header-modal');
            const openModalBtn = document.getElementById('open-header-modal-btn');
            const closeModalBtn = document.getElementById('close-header-modal-btn');
            const alertBox = document.getElementById('ejm-alert');
            const headerForm = document.getElementById('pce-header-form');
            const itemForm = document.getElementById('pce-item-form');
            const shapeSelect = document.getElementById('item-shape-id');
            const typeSelect = document.getElementById('item-type-config-id');
            const sizeNbInput = document.getElementById('item-size-nb');
            const nocInput = document.getElementById('item-noc');
            const nocHint = document.getElementById('item-noc-hint');
            const itemSummaryBody = document.getElementById('item-summary-body');
            const itemIdInput = document.getElementById('item-id');

            const val = (id) => document.getElementById(id)?.value || '';
            const setVal = (id, value) => { const el = document.getElementById(id); if (el) el.value = value ?? ''; };
            const nullable = (value) => (value === '' || value === null || typeof value === 'undefined') ? null : value;
            const csrfToken = (form) => form.querySelector('input[name="_token"]')?.value || '';

            function setAlert(message, type) {
                alertBox.textContent = message;
                alertBox.className = 'rounded-lg border px-4 py-3 text-sm';
                alertBox.classList.add(type === 'success' ? 'border-emerald-300' : 'border-red-300');
                alertBox.classList.add(type === 'success' ? 'bg-emerald-50' : 'bg-red-50');
                alertBox.classList.add(type === 'success' ? 'text-emerald-700' : 'text-red-700');
                alertBox.classList.remove('hidden');
            }

            function refreshTypeByShape() {
                const shapeId = shapeSelect?.value || '';
                if (!typeSelect) return;
                Array.from(typeSelect.options).forEach((option, idx) => {
                    if (idx === 0) return;
                    const optionShapeId = option.getAttribute('data-shape-id');
                    option.hidden = !!(shapeId && optionShapeId && optionShapeId !== shapeId);
                });
                if (typeSelect.options[typeSelect.selectedIndex]?.hidden) typeSelect.value = '';
            }

            async function loadValidationByNb() {
                const nb = sizeNbInput?.value;
                if (!nb) return;
                const response = await fetch(`${lookupUrl}?size_nb=${encodeURIComponent(nb)}`, { headers: { 'Accept': 'application/json' } });
                const json = await response.json();
                if (!response.ok) throw new Error(json.message || 'Lookup validasi gagal.');
                setVal('item-id-mm', json.id_mm ?? '');
                setVal('item-od-mm', json.od_mm ?? '');
                setVal('item-thk-mm', json.thk_mm ?? '');
                setVal('item-ply', json.ply ?? '');
                const maxNoc = Number(json.max_noc || 0);
                if (maxNoc > 0) {
                    nocInput.max = String(maxNoc);
                    if (!nocInput.value) nocInput.value = String(json.default_noc || maxNoc);
                    nocHint.textContent = `Maksimal NOC untuk NB ${nb}: ${maxNoc}`;
                }
            }

            function resetItemForm() {
                itemIdInput.value = '';
                setVal('item-shape-id', ''); setVal('item-type-config-id', ''); setVal('item-size-nb', ''); setVal('item-quantity', '1');
                setVal('item-noc', ''); setVal('item-id-mm', ''); setVal('item-od-mm', ''); setVal('item-thk-mm', ''); setVal('item-ply', '');
                setVal('item-material-bellow-id', ''); setVal('item-material-flange-id', ''); setVal('item-material-pipe-end-id', ''); setVal('item-status', 'DRAFT');
            }
            openModalBtn?.addEventListener('click', () => { modal.classList.remove('hidden'); modal.classList.add('flex'); });
            closeModalBtn?.addEventListener('click', () => { modal.classList.add('hidden'); modal.classList.remove('flex'); });
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            headerForm?.addEventListener('submit', async function (event) {
                event.preventDefault();
                const payload = {
                    pce_number: val('pce-number'),
                    project_of_name: nullable(val('project-of-name')) || 'Expansion Joint Metal',
                    end_user_id: nullable(val('end-user-id')),
                    area: nullable(val('area')),
                    drawing_no: nullable(val('drawing-no')),
                    document_no: nullable(val('document-no')),
                    revision: nullable(val('revision')),
                    date: nullable(val('header-date')),
                    sales_id: nullable(val('sales-id')),
                    status: 'DRAFT',
                };

                try {
                    const response = await fetch(createHeaderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(headerForm),
                        },
                        body: JSON.stringify(payload),
                    });
                    const json = await response.json();
                    if (!response.ok) throw new Error(json.message || 'Create header gagal.');
                    window.location.href = `${calcPageUrl}?header_id=${json.data.id}`;
                } catch (error) {
                    setAlert(error.message, 'error');
                }
            });

            itemForm?.addEventListener('submit', async function (event) {
                event.preventDefault();
                if (selectedHeaderId <= 0) {
                    setAlert('Pilih atau buat PCE Header dulu.', 'error');
                    return;
                }

                const itemId = Number(itemIdInput.value || 0);
                const payload = {
                    shape_id: nullable(val('item-shape-id')),
                    type_config_id: nullable(val('item-type-config-id')),
                    size_nb: nullable(val('item-size-nb')),
                    quantity: nullable(val('item-quantity')) || 1,
                    noc: nullable(val('item-noc')),
                    material_bellow_id: nullable(val('item-material-bellow-id')),
                    material_flange_id: nullable(val('item-material-flange-id')),
                    material_pipe_end_id: nullable(val('item-material-pipe-end-id')),
                    status: nullable(val('item-status')) || 'DRAFT',
                };

                const targetUrl = itemId > 0
                    ? updateItemUrlTemplate.replace('__HID__', String(selectedHeaderId)).replace('__IID__', String(itemId))
                    : createItemUrlTemplate.replace('__ID__', String(selectedHeaderId));
                const method = itemId > 0 ? 'PUT' : 'POST';

                try {
                    const response = await fetch(targetUrl, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(itemForm),
                        },
                        body: JSON.stringify(payload),
                    });
                    const json = await response.json();
                    if (!response.ok) throw new Error(json.message || 'Simpan item gagal.');
                    window.location.href = `${calcPageUrl}?header_id=${selectedHeaderId}`;
                } catch (error) {
                    setAlert(error.message, 'error');
                }
            });

            itemSummaryBody?.addEventListener('click', async function (event) {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;
                const payloadRaw = target.getAttribute('data-edit-item');
                if (!payloadRaw) return;

                try {
                    const item = JSON.parse(payloadRaw);
                    itemIdInput.value = item.id || '';
                    setVal('item-shape-id', item.shape_id || '');
                    refreshTypeByShape();
                    setVal('item-type-config-id', item.type_config_id || '');
                    setVal('item-size-nb', item.size_nb || '');
                    await loadValidationByNb();
                    setVal('item-quantity', item.quantity || 1);
                    setVal('item-noc', item.noc || '');
                    setVal('item-material-bellow-id', item.material_bellow_id || '');
                    setVal('item-material-flange-id', item.material_flange_id || '');
                    setVal('item-material-pipe-end-id', item.material_pipe_end_id || '');
                    setVal('item-status', item.status || 'DRAFT');
                    setAlert('Mode edit item aktif.', 'success');
                } catch (_) {
                    setAlert('Gagal membaca item.', 'error');
                }
            });

            document.getElementById('reset-item-btn')?.addEventListener('click', resetItemForm);
            shapeSelect?.addEventListener('change', refreshTypeByShape);
            sizeNbInput?.addEventListener('change', async function () {
                try {
                    await loadValidationByNb();
                } catch (error) {
                    setAlert(error.message, 'error');
                }
            });
            refreshTypeByShape();
        });
    </script>
</x-app-layout>
