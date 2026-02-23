<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight">Edit Product</h2>
                <p class="text-sm text-gray-300">Ubah data product</p>
            </div>

            <a href="{{ route('master.products.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15 border border-white/10">
                <span class="material-symbols-rounded text-[20px]">arrow_back</span>
                Back
            </a>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 space-y-4">

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <div class="font-semibold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-6">
            <form method="POST" action="{{ route('master.products.update', $product) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf
                @method('PUT')

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-600">Product Code</label>
                    <input name="product_code" value="{{ old('product_code', $product->product_code) }}" required
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300" />
                </div>

                <div class="md:col-span-6">
                    <label class="text-xs font-semibold text-gray-600">Product Name</label>
                    <input name="product_name" value="{{ old('product_name', $product->product_name) }}" required
                           class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300" />
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-600">Active</label>
                    <select name="is_active"
                            class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300">
                        <option value="1" @selected(old('is_active', (int)$product->is_active)===1)>Yes</option>
                        <option value="0" @selected(old('is_active', (int)$product->is_active)===0)>No</option>
                    </select>
                </div>

                <div class="md:col-span-12 flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        <span class="material-symbols-rounded text-[20px]">save</span>
                        Update
                    </button>

                    <a href="{{ route('master.products.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>