<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Quotation Detail</h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="mx-auto max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <p><span class="font-semibold text-slate-700">Quotation No:</span> {{ $quotation->quotation_no }}</p>
                    <p><span class="font-semibold text-slate-700">Date:</span> {{ optional($quotation->quotation_date)->format('Y-m-d') }}</p>
                    <p><span class="font-semibold text-slate-700">Revision No:</span> {{ $quotation->revision_no ?? 0 }}</p>
                    <p><span class="font-semibold text-slate-700">Customer:</span> {{ $quotation->company->company_name ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Marketing:</span> {{ $quotation->marketing->name ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Result Status:</span> {{ $quotation->result_status ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Status:</span> {{ $quotation->status }}</p>
                </div>
                <div class="space-y-3">
                    <p><span class="font-semibold text-slate-700">Attention:</span> {{ $quotation->attention ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Delivery To:</span> {{ $quotation->delivery_to ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Delivery Term:</span> {{ $quotation->delivery_term ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Payment:</span> {{ $quotation->payment_days ?? '-' }} days after delivery & invoice</p>
                    <p><span class="font-semibold text-slate-700">Delivery Time:</span> {{ $quotation->delivery_time_days ?? '-' }} days after PO received</p>
                    <p><span class="font-semibold text-slate-700">Scope of Work:</span> {{ $quotation->scope_of_work ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-700">Price Validity:</span> {{ $quotation->price_validity_weeks ?? '-' }} weeks after quotation date</p>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-slate-700">Address</label>
                <div class="mt-2 whitespace-pre-line rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">{{ $quotation->company_address ?? '-' }}</div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('quotations.index') }}"
                   class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">
                    Back
                </a>
                @can('quotations.edit')
                    <a href="{{ route('quotations.index', ['edit' => $quotation->id]) }}"
                       class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit
                    </a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
