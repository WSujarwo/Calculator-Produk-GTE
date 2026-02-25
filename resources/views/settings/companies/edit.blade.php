<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">
                Edit Company
            </h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/40 p-8">

            <form action="{{ route('settings.companies.update', $company) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Code</label>
                    <input type="text" name="company_code"
                           value="{{ old('company_code', $company->company_code) }}"
                           class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" name="company_name"
                           value="{{ old('company_name', $company->company_name) }}"
                           class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address"
                              rows="3"
                              class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $company->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $company->email) }}"
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $company->phone) }}"
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('settings.companies.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">
                        Update Company
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
