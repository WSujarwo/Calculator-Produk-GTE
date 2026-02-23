<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Create Shape</h2>
            <p class="text-sm text-gray-600">Tambah shape baru</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4 text-gray-900">
        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <div class="font-semibold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-6">
            <form method="POST" action="{{ route('master.shapes.store') }}"
                  class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Shape Code</label>
                    <input name="shape_code" value="{{ old('shape_code') }}" required
                           placeholder="CIRCULAR"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-6">
                    <label class="text-xs font-semibold text-gray-700">Shape Name</label>
                    <input name="shape_name" value="{{ old('shape_name') }}" required
                           placeholder="Circular"
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-700">Active</label>
                    <select name="is_active"
                            class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        <option value="1" @selected(old('is_active','1')=='1')>Yes</option>
                        <option value="0" @selected(old('is_active')=='0')>No</option>
                    </select>
                </div>

                <div class="md:col-span-12 flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">save</span> Save
                    </button>

                    <a href="{{ route('master.shapes.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        <span class="material-symbols-rounded text-[20px]">arrow_back</span> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>