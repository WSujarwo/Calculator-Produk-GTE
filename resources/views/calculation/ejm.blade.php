<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">
                Welcome, {{ auth()->user()->name }}
            </h2>
            <p class="text-sm text-gray-300">
                Role:
                <span class="font-semibold text-indigo-300">
                    {{ auth()->user()->getRoleNames()->first() }}
                </span>
            </p>
        </div>
    </x-slot>

    {{-- CONTENT --}}
    <div class="w-full px-6 lg:px-10 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- TOTAL QTN --}}
            <div class="rounded-2xl bg-white shadow-lg border border-gray-200/40 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Quotation (QTN)</p>
                        <h3 class="mt-2 text-4xl font-bold text-gray-900">
                            {{ $totalQtn ?? 0 }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">Total data quotation di sistem</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-3">
                        <span class="material-symbols-rounded text-[28px] text-gray-800">
                            request_quote
                        </span>
                    </div>
                </div>

                <div class="mt-5 h-[1px] bg-gray-200/80"></div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Last update</span>
                    <span class="text-xs font-medium text-gray-700">{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>

            {{-- TOTAL ORDER --}}
            <div class="rounded-2xl bg-white shadow-lg border border-gray-200/40 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Order List</p>
                        <h3 class="mt-2 text-4xl font-bold text-gray-900">
                            {{ $totalOrder ?? 0 }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">Total data order yang tercatat</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-3">
                        <span class="material-symbols-rounded text-[28px] text-gray-800">
                            list_alt
                        </span>
                    </div>
                </div>

                <div class="mt-5 h-[1px] bg-gray-200/80"></div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Status</span>
                    <span class="text-xs font-semibold text-emerald-600">Active</span>
                </div>
            </div>

            {{-- TOTAL CUSTOMER --}}
            <div class="rounded-2xl bg-white shadow-lg border border-gray-200/40 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Customer</p>
                        <h3 class="mt-2 text-4xl font-bold text-gray-900">
                            {{ $totalCustomer ?? 0 }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">Total customer terdaftar</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-3">
                        <span class="material-symbols-rounded text-[28px] text-gray-800">
                            group
                        </span>
                    </div>
                </div>

                <div class="mt-5 h-[1px] bg-gray-200/80"></div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Role akses</span>
                    <span class="text-xs font-medium text-gray-700">
                        {{ auth()->user()->getRoleNames()->first() }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
