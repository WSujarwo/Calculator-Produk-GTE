<div class="grid grid-cols-1 gap-3 md:grid-cols-4">
    <label class="text-xs text-slate-600">
        <span class="font-semibold">PCE Number</span>
        <select name="pce_header_id" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih PCE Number --</option>
            @foreach ($pceHeaders as $header)
                <option value="{{ $header->id }}" @selected((string) old('pce_header_id', $item->pce_header_id ?? '') === (string) $header->id)>{{ $header->pce_number }}</option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Plat Number</span>
        <input type="text" name="plat_number" value="{{ old('plat_number', $item->plat_number ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Description</span>
        <input type="text" name="description" value="{{ old('description', $item->description ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Qty</span>
        <input type="number" name="qty" min="1" value="{{ old('qty', $item->qty ?? 1) }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Shape</span>
        <select name="shape_id" id="shape-id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih Shape --</option>
            @foreach ($shapes as $shape)
                <option value="{{ $shape->id }}" @selected((string) old('shape_id', $item->shape_id ?? '') === (string) $shape->id)>{{ $shape->shape_name }}</option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Type EJM</span>
        <select name="type_config_id" id="type-config-id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih Type --</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}" data-shape-id="{{ $type->shape_id }}" @selected((string) old('type_config_id', $item->type_config_id ?? '') === (string) $type->id)>{{ $type->type_name }}</option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">NB</span>
        <select name="nb" id="nb-select" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih NB --</option>
            @foreach ($nbOptions as $nb)
                <option value="{{ $nb->nb }}"
                        data-noc="{{ $nb->noc }}"
                        data-id-mm="{{ $nb->id_mm }}"
                        data-od-mm="{{ $nb->od_mm }}"
                        data-thk-mm="{{ $nb->thk }}"
                        data-ply="{{ $nb->ly }}"
                        @selected((string) old('nb', $item->nb ?? '') === (string) $nb->nb)>
                    {{ $nb->nb }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">NOC (max default)</span>
        <input type="number" name="noc" id="noc" min="0" value="{{ old('noc', $item->noc ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
        <p id="noc-hint" class="mt-1 text-[11px] text-slate-500">Pilih NB untuk melihat NOC default.</p>
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">ID (otomatis)</span>
        <input type="text" id="id-mm" value="{{ old('id_mm', $item->id_mm ?? '') }}" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-100 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">OD (otomatis)</span>
        <input type="text" id="od-mm" value="{{ old('od_mm', $item->od_mm ?? '') }}" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-100 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Thk (otomatis)</span>
        <input type="text" id="thk-mm" value="{{ old('thk_mm', $item->thk_mm ?? '') }}" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-100 text-sm">
    </label>

    <label class="text-xs text-slate-600">
        <span class="font-semibold">Ply (otomatis)</span>
        <input type="text" id="ply" value="{{ old('ply', $item->ply ?? '') }}" readonly class="mt-1 w-full rounded-lg border-slate-200 bg-slate-100 text-sm">
    </label>

    <label class="text-xs text-slate-600 md:col-span-2">
        <span class="font-semibold">Material Bellow</span>
        <select name="material_bellow_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih Material Bellow --</option>
            @foreach ($materialBellow as $material)
                <option value="{{ $material->id }}" @selected((string) old('material_bellow_id', $item->material_bellow_id ?? '') === (string) $material->id)>{{ $material->part_number }} | {{ $material->material }}</option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600 md:col-span-2">
        <span class="font-semibold">Material Flange</span>
        <select name="material_flange_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih Material Flange --</option>
            @foreach ($materialFlange as $material)
                <option value="{{ $material->id }}" @selected((string) old('material_flange_id', $item->material_flange_id ?? '') === (string) $material->id)>{{ $material->part_number }} | {{ $material->material }}</option>
            @endforeach
        </select>
    </label>

    <label class="text-xs text-slate-600 md:col-span-2">
        <span class="font-semibold">Material Pipe End</span>
        <select name="material_pipe_end_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            <option value="">-- Pilih Material Pipe End --</option>
            @foreach ($materialPipeEnd as $material)
                <option value="{{ $material->id }}" @selected((string) old('material_pipe_end_id', $item->material_pipe_end_id ?? '') === (string) $material->id)>{{ $material->part_number }} | {{ $material->material }}</option>
            @endforeach
        </select>
    </label>
</div>

<script>
    (function () {
        const nbSelect = document.getElementById('nb-select');
        const shapeSelect = document.getElementById('shape-id');
        const typeSelect = document.getElementById('type-config-id');
        const idMm = document.getElementById('id-mm');
        const odMm = document.getElementById('od-mm');
        const thkMm = document.getElementById('thk-mm');
        const ply = document.getElementById('ply');
        const nocInput = document.getElementById('noc');
        const nocHint = document.getElementById('noc-hint');

        nbSelect?.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const defaultNoc = selected?.dataset.noc || '';
            idMm.value = selected?.dataset.idMm || '';
            odMm.value = selected?.dataset.odMm || '';
            thkMm.value = selected?.dataset.thkMm || '';
            ply.value = selected?.dataset.ply || '';
            if (nocInput) {
                nocInput.max = defaultNoc;
                if (!nocInput.value) {
                    nocInput.value = defaultNoc;
                }
            }
            if (nocHint) {
                nocHint.textContent = defaultNoc ? `NOC default NB ini: ${defaultNoc}. Tidak boleh lebih besar dari nilai ini.` : 'Pilih NB untuk melihat NOC default.';
            }
        });

        shapeSelect?.addEventListener('change', function () {
            const shapeId = this.value;
            Array.from(typeSelect.options).forEach((option, idx) => {
                if (idx === 0) return;
                const optionShapeId = option.getAttribute('data-shape-id') || '';
                option.hidden = !!(shapeId && optionShapeId && optionShapeId !== shapeId);
            });
            if (typeSelect.options[typeSelect.selectedIndex]?.hidden) {
                typeSelect.value = '';
            }
        });

        shapeSelect?.dispatchEvent(new Event('change'));
        nbSelect?.dispatchEvent(new Event('change'));
    })();
</script>
