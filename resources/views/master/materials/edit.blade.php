<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit Material</h2>
            <p class="text-sm text-gray-600">Ubah data material</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 text-gray-900">
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-5">
            <form method="POST" action="{{ route('master.materials.update', $material) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf
                @method('PUT')

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Part Number</label>
                    <input type="text" name="part_number" value="{{ old('part_number', $material->part_number) }}" required
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('part_number') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Naming</label>
                    <input type="text" name="naming" value="{{ old('naming', $material->naming) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Quality</label>
                    <input type="text" name="quality" value="{{ old('quality', $material->quality) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Code1</label>
                    <input type="text" name="code1" value="{{ old('code1', $material->code1) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Code2</label>
                    <input type="text" name="code2" value="{{ old('code2', $material->code2) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Code3</label>
                    <input type="text" name="code3" value="{{ old('code3', $material->code3) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Thk</label>
                    <input type="text" name="thk" value="{{ old('thk', $material->thk) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">PriceSQM</label>
                    <input type="text" name="price_sqm" value="{{ old('price_sqm', $material->price_sqm) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">PriceKG</label>
                    <input type="text" name="price_kg" value="{{ old('price_kg', $material->price_kg) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">PriceGram</label>
                    <input type="text" name="price_gram" value="{{ old('price_gram', $material->price_gram) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Berat (gr)</label>
                    <input type="text" name="berat_gr" value="{{ old('berat_gr', $material->berat_gr) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Panjang (meter)</label>
                    <input type="text" name="panjang_meter" value="{{ old('panjang_meter', $material->panjang_meter) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Berat per Meter (gr)</label>
                    <input type="text" name="berat_per_meter_gr" value="{{ old('berat_per_meter_gr', $material->berat_per_meter_gr) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                </div>

                <div class="md:col-span-12">
                    <label class="text-xs font-semibold text-gray-700">Description</label>
                    <textarea name="description" rows="3"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">{{ old('description', $material->description) }}</textarea>
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <a href="{{ route('master.materials.index') }}"
                        class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">save</span>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
