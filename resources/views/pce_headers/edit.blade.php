<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit PCE Header</h2>
            <p class="text-sm text-gray-600">{{ $pceHeader->pce_number }}</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4 text-gray-900">
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

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-6">
            <form method="POST" action="{{ route('pce-headers.update', $pceHeader) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold text-gray-700">Nomor PCE</label>
                    <input type="text" name="pce_number" value="{{ old('pce_number', $pceHeader->pce_number) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" required>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Project Name</label>
                    <input type="text" name="project_name" value="{{ old('project_name', $pceHeader->project_name) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Area</label>
                    <input type="text" name="area" value="{{ old('area', $pceHeader->area) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Drawing No</label>
                    <input type="text" name="drawing_no" value="{{ old('drawing_no', $pceHeader->drawing_no) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Document No</label>
                    <input type="text" name="document_no" value="{{ old('document_no', $pceHeader->document_no) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Revision</label>
                    <input type="text" name="revision" value="{{ old('revision', $pceHeader->revision) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Tanggal PCE</label>
                    <input type="date" name="pce_date"
                           value="{{ old('pce_date', optional($pceHeader->pce_date)->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700">Status</label>
                    <select name="status"
                            class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        @php($currentStatus = old('status', $pceHeader->status))
                        @foreach(['DRAFT', 'OPEN', 'APPROVED', 'REJECTED', 'CLOSED'] as $status)
                            <option value="{{ $status }}" @selected($currentStatus === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">save</span>
                        Simpan
                    </button>
                    <a href="{{ route('pcelist') }}"
                       class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

