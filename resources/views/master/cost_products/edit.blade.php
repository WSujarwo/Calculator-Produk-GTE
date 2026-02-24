<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit Cost Product</h2>
            <p class="text-sm text-gray-600">Ubah data cost product</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6 text-gray-900">
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 p-5">
            <form method="POST" action="{{ route('master.cost-products.update', $costProduct) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf
                @method('PUT')

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">DLABORNO</label>
                    <input type="text" name="dlaborno" value="{{ old('dlaborno', $costProduct->dlaborno) }}" required
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('dlaborno') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">COST (IDR)</label>
                    <input type="text" name="cost" value="{{ old('cost', $costProduct->cost) }}" placeholder="contoh: 24900 atau Rp 24.900"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('cost') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">GLACCOUNT</label>
                    <input type="text" name="glaccount" value="{{ old('glaccount', $costProduct->glaccount) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('glaccount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">STATUS</label>
                    <input type="text" name="status" value="{{ old('status', $costProduct->status) }}" placeholder="1/0 atau Aktif/Nonaktif"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">ACCOUNTNAME</label>
                    <input type="text" name="accountname" value="{{ old('accountname', $costProduct->accountname) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('accountname') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="text-xs font-semibold text-gray-700">Statuse</label>
                    <input type="text" name="statuse" value="{{ old('statuse', $costProduct->statuse) }}"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />
                    @error('statuse') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-12">
                    <label class="text-xs font-semibold text-gray-700">DESCRIPTION</label>
                    <textarea name="description" rows="3"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">{{ old('description', $costProduct->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-12 flex justify-end gap-2">
                    <a href="{{ route('master.cost-products.index') }}"
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
