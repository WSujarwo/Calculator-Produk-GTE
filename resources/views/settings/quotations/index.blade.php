<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Quotation Management</h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-100 p-4 text-green-700">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-200/40 bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Quotation List</h3>
                    <form method="GET" action="{{ route('quotations.index') }}" class="flex items-center gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? request('search') }}"
                            placeholder="Search no/customer/marketing"
                            class="w-72 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <button type="submit" class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            Search
                        </button>
                        @if (!empty($search))
                            <a href="{{ route('quotations.index') }}" class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
                @can('quotations.create')
                    <button type="button" id="openCreateModal"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm text-white transition hover:bg-indigo-700">
                        + Add Quotation
                    </button>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Quotation No</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Customer</th>
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
                                <td class="px-4 py-3">{{ $quotation->result_status }}</td>
                                <td class="flex gap-3 px-4 py-3">
                                    @can('quotations.view')
                                        <a href="{{ route('quotations.show', $quotation) }}"
                                           class="text-sm text-slate-600 hover:underline">View</a>
                                    @endcan

                                    @can('quotations.edit')
                                        <button type="button"
                                                class="openEditModal text-sm text-indigo-600 hover:underline"
                                                data-id="{{ $quotation->id }}"
                                                data-date="{{ optional($quotation->quotation_date)->format('Y-m-d') }}"
                                                data-company_id="{{ $quotation->company_id }}"
                                                data-marketing_id="{{ $quotation->marketing_id }}"
                                                data-revision_no="{{ $quotation->revision_no }}"
                                                data-attention="{{ $quotation->attention }}"
                                                data-delivery_to="{{ $quotation->delivery_to }}"
                                                data-delivery_term="{{ $quotation->delivery_term }}"
                                                data-payment_days="{{ $quotation->payment_days }}"
                                                data-delivery_time_days="{{ $quotation->delivery_time_days }}"
                                                data-scope_of_work="{{ $quotation->scope_of_work }}"
                                                data-price_validity_weeks="{{ $quotation->price_validity_weeks }}"
                                                data-company_address="{{ $quotation->company_address }}"
                                                data-result_status="{{ $quotation->result_status }}">
                                            Edit
                                        </button>
                                    @endcan

                                    @can('quotations.delete')
                                        <form action="{{ route('quotations.destroy', $quotation) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this quotation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-sm text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No quotation data found</td>
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

    @can('quotations.create')
    <div id="createModal" class="fixed inset-0 z-50 {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl lg:p-8">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Quotation</h3>
                <button type="button" data-close-modal="createModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <form action="{{ route('quotations.store') }}" method="POST" class="space-y-6">
                @csrf
                @if (!empty($returnTo))
                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Quotation No</label>
                        <div class="mt-2 grid grid-cols-[170px,1fr] gap-2">
                            <input type="text" value="GTE-QTN-" class="w-full rounded-xl border-slate-300 bg-slate-100 text-sm text-slate-500" disabled>
                            <input type="text" name="quotation_suffix" value="{{ old('quotation_suffix') }}" maxlength="6" placeholder="XXXXXX" class="w-full rounded-xl border-slate-300 text-sm uppercase focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        @error('quotation_suffix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Result Status</label>
                        <select name="result_status" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                            @foreach (['GAGAL', 'PENDING', 'SUKSES'] as $status)
                                <option value="{{ $status }}" @selected(old('result_status', 'PENDING') === $status) disabled>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Date</label>
                            <input type="date" name="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Marketing</label>
                                <button type="button" id="openMarketingModal" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">+ Add Marketing</button>
                            </div>
                            <select name="marketing_id" id="createMarketingId" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">-- Select marketing --</option>
                                @foreach ($marketingOptions as $opt)
                                    <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Revision No</label>
                            <input type="number" min="0" name="revision_no" value="{{ old('revision_no', 0) }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Customer Name</label>
                                <button type="button" id="openCustomerModal" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">+ Add Customer</button>
                            </div>
                            <select name="company_id" id="createCompanyId" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">-- Select customer --</option>
                                @foreach ($customerOptions as $opt)
                                    <option value="{{ $opt['id'] }}" data-address="{{ $opt['address'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Address</label>
                            <textarea name="company_address" id="createCompanyAddress" rows="4" class="mt-2 w-full rounded-xl border-slate-300 text-sm">{{ old('company_address') }}</textarea>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700">Attention</label><input type="text" name="attention" value="{{ old('attention') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery To</label><input type="text" name="delivery_to" value="{{ old('delivery_to') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery Term</label><input type="text" name="delivery_term" value="{{ old('delivery_term') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="payment_days" value="{{ old('payment_days') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after delivery & invoice</span></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="delivery_time_days" value="{{ old('delivery_time_days') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after PO received</span></div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Scope of Work</label>
                            <select name="scope_of_work" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                                @foreach (['Supply Only', 'Supply + Installation', 'Installation Only'] as $scope)
                                    <option value="{{ $scope }}" @selected(old('scope_of_work', 'Supply Only') === $scope)>{{ $scope }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="price_validity_weeks" value="{{ old('price_validity_weeks') }}" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">weeks after quotation date</span></div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button type="button" data-close-modal="createModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Close</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('quotations.edit')
    <div id="editModal" class="fixed inset-0 z-50 {{ $editing ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 px-4">
        <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl lg:p-8">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Edit Quotation</h3>
                <button type="button" data-close-modal="editModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>

            <form id="editForm" action="{{ $editing ? route('quotations.update', $editing) : '#' }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Quotation No</label>
                        <input type="text" id="editQuotationNo" value="{{ $editing->quotation_no ?? '' }}" class="mt-2 w-full rounded-xl border-slate-300 bg-slate-100 text-sm text-slate-500" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Result Status</label>
                        <select name="result_status" id="editResultStatus" class="mt-2 w-full rounded-xl border-slate-300 text-sm">
                            @foreach (['GAGAL', 'PENDING', 'SUKSES'] as $status)
                                <option value="{{ $status }}" @selected(($editing->result_status ?? 'PENDING') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700">Date</label><input type="date" name="quotation_date" id="editQuotationDate" value="{{ optional($editing->quotation_date ?? null)->format('Y-m-d') }}" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="text-sm font-medium text-slate-700">Marketing</label><select name="marketing_id" id="editMarketingId" class="mt-2 w-full rounded-xl border-slate-300 text-sm"><option value="">-- Select marketing --</option>@foreach ($marketingOptions as $opt)<option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-slate-700">Revision No</label><input type="number" min="0" name="revision_no" id="editRevisionNo" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="text-sm font-medium text-slate-700">Customer Name</label><select name="company_id" id="editCompanyId" class="mt-2 w-full rounded-xl border-slate-300 text-sm"><option value="">-- Select customer --</option>@foreach ($customerOptions as $opt)<option value="{{ $opt['id'] }}" data-address="{{ $opt['address'] }}">{{ $opt['label'] }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-slate-700">Address</label><textarea name="company_address" id="editCompanyAddress" rows="4" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></textarea></div>
                    </div>
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700">Attention</label><input type="text" name="attention" id="editAttention" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery To</label><input type="text" name="delivery_to" id="editDeliveryTo" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700">Delivery Term</label><input type="text" name="delivery_term" id="editDeliveryTerm" class="mt-2 w-full rounded-xl border-slate-300 text-sm"></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="payment_days" id="editPaymentDays" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after delivery & invoice</span></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="delivery_time_days" id="editDeliveryTimeDays" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">days after PO received</span></div>
                        <div><label class="block text-sm font-medium text-slate-700">Scope of Work</label><select name="scope_of_work" id="editScopeOfWork" class="mt-2 w-full rounded-xl border-slate-300 text-sm">@foreach (['Supply Only', 'Supply + Installation', 'Installation Only'] as $scope)<option value="{{ $scope }}">{{ $scope }}</option>@endforeach</select></div>
                        <div class="grid grid-cols-[120px,1fr] items-center gap-3"><input type="number" min="0" name="price_validity_weeks" id="editPriceValidityWeeks" class="rounded-xl border-slate-300 text-sm"><span class="text-sm text-slate-700">weeks after quotation date</span></div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button type="button" data-close-modal="editModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Close</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Update and Close</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <div id="customerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Add Customer</h3>
                <button type="button" data-close-modal="customerModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <form id="customerInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="company_code" placeholder="Customer Code" class="w-full rounded-xl border-slate-300 text-sm" required>
                <input type="text" name="company_name" placeholder="Customer Name" class="w-full rounded-xl border-slate-300 text-sm" required>
                <textarea name="address" placeholder="Address" rows="3" class="w-full rounded-xl border-slate-300 text-sm"></textarea>
                <div class="grid gap-3 md:grid-cols-2">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-slate-300 text-sm">
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <p id="customerInlineError" class="hidden text-sm text-red-600"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="customerModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-700">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="marketingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Add Marketing</h3>
                <button type="button" data-close-modal="marketingModal" class="text-slate-500 hover:text-slate-700">x</button>
            </div>
            <form id="marketingInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="marketing_no" placeholder="Marketing No" class="w-full rounded-xl border-slate-300 text-sm" required>
                <input type="text" name="name" placeholder="Name" class="w-full rounded-xl border-slate-300 text-sm" required>
                <div class="grid gap-3 md:grid-cols-2">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-slate-300 text-sm" required>
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-slate-300 text-sm">
                </div>
                <p id="marketingInlineError" class="hidden text-sm text-red-600"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="marketingModal" class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-700">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            function toggleModal(id, show) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.toggle('hidden', !show);
                modal.classList.toggle('flex', show);
            }

            function syncAddress(selectId, addressId) {
                const select = document.getElementById(selectId);
                const address = document.getElementById(addressId);
                if (!select || !address) return;
                select.addEventListener('change', function () {
                    const selected = select.options[select.selectedIndex];
                    const addr = selected ? (selected.getAttribute('data-address') || '') : '';
                    if (addr && !address.value) {
                        address.value = addr;
                    }
                });
            }

            document.getElementById('openCreateModal')?.addEventListener('click', function () {
                toggleModal('createModal', true);
            });

            document.querySelectorAll('.openEditModal').forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const template = "{{ route('quotations.update', ['quotation' => '__ID__']) }}";
                    document.getElementById('editForm').setAttribute('action', template.replace('__ID__', id));

                    document.getElementById('editQuotationDate').value = this.getAttribute('data-date') || '';
                    document.getElementById('editCompanyId').value = this.getAttribute('data-company_id') || '';
                    document.getElementById('editMarketingId').value = this.getAttribute('data-marketing_id') || '';
                    document.getElementById('editRevisionNo').value = this.getAttribute('data-revision_no') || 0;
                    document.getElementById('editAttention').value = this.getAttribute('data-attention') || '';
                    document.getElementById('editDeliveryTo').value = this.getAttribute('data-delivery_to') || '';
                    document.getElementById('editDeliveryTerm').value = this.getAttribute('data-delivery_term') || '';
                    document.getElementById('editPaymentDays').value = this.getAttribute('data-payment_days') || '';
                    document.getElementById('editDeliveryTimeDays').value = this.getAttribute('data-delivery_time_days') || '';
                    document.getElementById('editScopeOfWork').value = this.getAttribute('data-scope_of_work') || 'Supply Only';
                    document.getElementById('editPriceValidityWeeks').value = this.getAttribute('data-price_validity_weeks') || '';
                    document.getElementById('editCompanyAddress').value = this.getAttribute('data-company_address') || '';
                    document.getElementById('editResultStatus').value = this.getAttribute('data-result_status') || 'PENDING';

                    toggleModal('editModal', true);
                });
            });

            document.getElementById('openCustomerModal')?.addEventListener('click', function () {
                toggleModal('customerModal', true);
            });
            document.getElementById('openMarketingModal')?.addEventListener('click', function () {
                toggleModal('marketingModal', true);
            });

            document.querySelectorAll('[data-close-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    toggleModal(this.getAttribute('data-close-modal'), false);
                });
            });

            syncAddress('createCompanyId', 'createCompanyAddress');
            syncAddress('editCompanyId', 'editCompanyAddress');

            async function postInline(formId, url, errorId, onSuccess) {
                const form = document.getElementById(formId);
                const error = document.getElementById(errorId);
                if (!form || !error) return;
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    error.classList.add('hidden');
                    error.textContent = '';
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Failed to save data.');
                        onSuccess(data);
                        form.reset();
                    } catch (errorObj) {
                        error.textContent = errorObj.message;
                        error.classList.remove('hidden');
                    }
                });
            }

            postInline(
                'customerInlineForm',
                "{{ route('quotations.inline-companies.store') }}",
                'customerInlineError',
                function (data) {
                    const createSelect = document.getElementById('createCompanyId');
                    const editSelect = document.getElementById('editCompanyId');
                    [createSelect, editSelect].forEach(function (select) {
                        const option = document.createElement('option');
                        option.value = String(data.id);
                        option.textContent = data.label;
                        option.setAttribute('data-address', data.address || '');
                        select.appendChild(option);
                        select.value = String(data.id);
                    });
                    document.getElementById('createCompanyAddress').value = data.address || '';
                    document.getElementById('editCompanyAddress').value = data.address || '';
                    toggleModal('customerModal', false);
                }
            );

            postInline(
                'marketingInlineForm',
                "{{ route('quotations.inline-marketings.store') }}",
                'marketingInlineError',
                function (data) {
                    const createSelect = document.getElementById('createMarketingId');
                    const editSelect = document.getElementById('editMarketingId');
                    [createSelect, editSelect].forEach(function (select) {
                        const option = document.createElement('option');
                        option.value = String(data.id);
                        option.textContent = data.label;
                        select.appendChild(option);
                        select.value = String(data.id);
                    });
                    toggleModal('marketingModal', false);
                }
            );

            @if ($editing)
                document.getElementById('editMarketingId').value = "{{ $editing->marketing_id }}";
                document.getElementById('editCompanyId').value = "{{ $editing->company_id }}";
                document.getElementById('editRevisionNo').value = "{{ $editing->revision_no ?? 0 }}";
                document.getElementById('editAttention').value = @json($editing->attention);
                document.getElementById('editDeliveryTo').value = @json($editing->delivery_to);
                document.getElementById('editDeliveryTerm').value = @json($editing->delivery_term);
                document.getElementById('editPaymentDays').value = @json($editing->payment_days);
                document.getElementById('editDeliveryTimeDays').value = @json($editing->delivery_time_days);
                document.getElementById('editScopeOfWork').value = @json($editing->scope_of_work ?? 'Supply Only');
                document.getElementById('editPriceValidityWeeks').value = @json($editing->price_validity_weeks);
                document.getElementById('editCompanyAddress').value = @json($editing->company_address);
                document.getElementById('editResultStatus').value = @json($editing->result_status ?? 'PENDING');
            @endif
        })();
    </script>
</x-app-layout>
