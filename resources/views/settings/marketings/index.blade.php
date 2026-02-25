<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">
                Marketing Management
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
                <h3 class="text-lg font-semibold text-gray-800">
                    Marketing List
                </h3>

                <a href="{{ route('settings.marketings.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 transition">
                    + Add Marketing
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($marketings as $mkt)
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $mkt->marketing_no }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $mkt->name }}
                                </td>
                                <td class="px-4 py-3">{{ $mkt->email }}</td>
                                <td class="px-4 py-3">{{ $mkt->phone ?? '-' }}</td>
                                <td class="px-4 py-3 flex gap-3">
                                    <a href="{{ route('settings.marketings.edit', $mkt) }}"
                                       class="text-indigo-600 hover:underline text-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('settings.marketings.destroy', $mkt) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this marketing user?')">
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
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    No marketing data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $marketings->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
