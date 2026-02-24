<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">List Customer (Dummy)</h2>
            <p class="text-sm text-gray-600">Data sementara untuk tampilan awal.</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/60 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Data Customer</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Code</th>
                            <th class="px-3 py-2 text-left font-semibold">Nama Customer</th>
                            <th class="px-3 py-2 text-left font-semibold">Kota</th>
                            <th class="px-3 py-2 text-left font-semibold">PIC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($customers as $customer)
                            <tr>
                                <td class="px-3 py-3 font-semibold text-gray-900">{{ $customer['code'] }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $customer['name'] }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $customer['city'] }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $customer['pic'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

