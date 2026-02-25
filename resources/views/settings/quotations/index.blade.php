<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">
                Quotation Management
            </h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/40 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Quotation List</h3>
                <a href="{{ route('settings.quotations.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 transition">
                    + Add Quotation
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Quotation No</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3">Marketing</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotations as $quotation)
                            <tr class="border-b">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $quotation->quotation_no }}</td>
                                <td class="px-4 py-3">{{ optional($quotation->quotation_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $quotation->company->company_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $quotation->marketing->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $quotation->status }}</td>
                                <td class="px-4 py-3 flex gap-3">
                                    <a href="{{ route('settings.quotations.edit', $quotation) }}"
                                       class="text-indigo-600 hover:underline text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('settings.quotations.destroy', $quotation) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this quotation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                    No quotation data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $quotations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
