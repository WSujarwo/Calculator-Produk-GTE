
<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">List PCE Order</h2>
                <p class="text-sm text-gray-600">Daftar PCE Header</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4 text-gray-900">
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

        <div class="flex items-center justify-end">
            @can('pce-headers.create')
                <button type="button"
                        data-modal-target="create-pce-modal"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    <span class="material-symbols-rounded text-[20px]">add</span>
                    Create PCE
                </button>
            @endcan
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-900">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Nomor PCE</th>
                            <th class="px-4 py-3 text-left font-semibold">Quantity Item</th>
                            <th class="px-4 py-3 text-left font-semibold">Grand Total (IDR)</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($headers as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    {{ ($headers->currentPage() - 1) * $headers->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3 font-semibold">{{ $row->pce_number }}</td>
                                <td class="px-4 py-3">{{ (int)($row->quantity_item ?? 0) }}</td>
                                <td class="px-4 py-3">Rp {{ number_format((float)($row->grand_total_idr ?? 0), 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-800">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    @can('pce-headers.edit')
                                        <button type="button"
                                                data-modal-target="edit-pce-modal-{{ $row->id }}"
                                                class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 hover:bg-gray-50">
                                            <span class="material-symbols-rounded text-[18px]">edit</span>
                                            Edit
                                        </button>
                                    @endcan
                                    <a href="{{ route('pce-headers.show', $row) }}"
                                       class="inline-flex items-center gap-1 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        <span class="material-symbols-rounded text-[18px]">visibility</span>
                                        Lihat
                                    </a>
                                    @can('pce-headers.delete')
                                        <form action="{{ route('pce-headers.destroy', $row) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin hapus data PCE ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                <span class="material-symbols-rounded text-[18px]">delete</span>
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-600">Tidak ada data PCE.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4">
                {{ $headers->links() }}
            </div>
        </div>
    </div>

    @can('pce-headers.create')
        <div id="create-pce-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                    <h3 class="text-lg font-bold text-gray-900">Create PCE Header</h3>
                    <button type="button" data-modal-close class="rounded-lg p-1 text-gray-500 hover:bg-gray-100">✕</button>
                </div>
                <form method="POST" action="{{ route('pce-headers.store') }}" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Nomor PCE</label>
                        <input type="text" name="pce_number" value="{{ old('pce_number') }}" required
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Project Name</label>
                        <input type="text" name="project_name" value="{{ old('project_name', 'Expansion Joint Metal') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">End User (Company / Customer)</label>
                        <select name="end_user_id" class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                            <option value="">- Pilih Company / Customer -</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected((string)old('end_user_id') === (string)$company->id)>
                                    {{ $company->company_code }} - {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Area</label>
                        <input type="text" name="area" value="{{ old('area') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Drawing No</label>
                        <input type="text" name="drawing_no" value="{{ old('drawing_no') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Document No</label>
                        <input type="text" name="document_no" value="{{ old('document_no') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Revision</label>
                        <input type="text" name="revision" value="{{ old('revision') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Tanggal PCE</label>
                        <input type="date" name="pce_date" value="{{ old('pce_date') }}"
                               class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Sales (Marketing)</label>
                        <select name="sales_user_id" class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                            <option value="">- Pilih Marketing -</option>
                            @foreach($marketings as $marketing)
                                <option value="{{ $marketing->id }}" @selected((string)old('sales_user_id') === (string)$marketing->id)>
                                    {{ $marketing->marketing_no }} - {{ $marketing->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        Status default saat create: <span class="font-semibold">PENDING</span>
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                        <button type="button" data-modal-close class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    @can('pce-headers.edit')
        @foreach($headers as $row)
            <div id="edit-pce-modal-{{ $row->id }}" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                        <h3 class="text-lg font-bold text-gray-900">Edit PCE Header - {{ $row->pce_number }}</h3>
                        <button type="button" data-modal-close class="rounded-lg p-1 text-gray-500 hover:bg-gray-100">✕</button>
                    </div>
                    <form method="POST" action="{{ route('pce-headers.update', $row) }}" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Nomor PCE</label>
                            <input type="text" name="pce_number" value="{{ $row->pce_number }}" required
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Project Name</label>
                            <input type="text" name="project_name" value="{{ $row->project_name }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">End User (Company / Customer)</label>
                            <select name="end_user_id" class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                                <option value="">- Pilih Company / Customer -</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected((string)$row->end_user_id === (string)$company->id)>
                                        {{ $company->company_code }} - {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Area</label>
                            <input type="text" name="area" value="{{ $row->area }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Drawing No</label>
                            <input type="text" name="drawing_no" value="{{ $row->drawing_no }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Document No</label>
                            <input type="text" name="document_no" value="{{ $row->document_no }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Revision</label>
                            <input type="text" name="revision" value="{{ $row->revision }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Tanggal PCE</label>
                            <input type="date" name="pce_date" value="{{ optional($row->pce_date)->format('Y-m-d') }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Sales (Marketing)</label>
                            <select name="sales_user_id" class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                                <option value="">- Pilih Marketing -</option>
                                @foreach($marketings as $marketing)
                                    <option value="{{ $marketing->id }}" @selected((string)$row->sales_user_id === (string)$marketing->id)>
                                        {{ $marketing->marketing_no }} - {{ $marketing->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700">Status</label>
                            <select name="status" class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                                @foreach(['PENDING', 'OPEN', 'APPROVED', 'REJECTED', 'CLOSED'] as $status)
                                    <option value="{{ $status }}" @selected($row->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                            <button type="button" data-modal-close class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endcan

    <script>
        document.addEventListener('click', function (e) {
            const opener = e.target.closest('[data-modal-target]');
            if (opener) {
                const modal = document.getElementById(opener.getAttribute('data-modal-target'));
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            const closer = e.target.closest('[data-modal-close]');
            if (closer) {
                const modal = closer.closest('.fixed.inset-0');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        });
    </script>
</x-app-layout>
