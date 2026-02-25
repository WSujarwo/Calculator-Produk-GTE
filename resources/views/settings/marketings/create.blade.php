<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">
                Create Marketing
            </h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/40 p-8">

            <form action="{{ route('settings.marketings.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Marketing No</label>
                    <input type="text" name="marketing_no"
                           value="{{ old('marketing_no') }}"
                           placeholder="Contoh: MKT-001"
                           class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('marketing_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name"
                           value="{{ old('name') }}"
                           class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone"
                               value="{{ old('phone') }}"
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('settings.marketings.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">
                        Save Marketing
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
