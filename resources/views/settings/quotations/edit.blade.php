<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Edit Quotation</h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="mx-auto max-w-5xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <form action="{{ route('settings.quotations.update', $quotation) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Quotation No</label>
                        <input type="text" value="{{ $quotation->quotation_no }}"
                               class="mt-2 w-full rounded-xl border-slate-300 bg-slate-100 text-sm text-slate-500" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Result Status</label>
                        <div class="mt-2 flex flex-wrap items-center gap-4 rounded-xl border border-slate-300 px-4 py-2.5">
                            @foreach (['GAGAL', 'PENDING', 'SUKSES'] as $status)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="radio" name="result_status" value="{{ $status }}"
                                           class="border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                           @checked(old('result_status', $quotation->result_status ?? 'PENDING') === $status)>
                                    {{ $status }}
                                </label>
                            @endforeach
                        </div>
                        @error('result_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Date</label>
                            <input type="date" name="quotation_date"
                                   value="{{ old('quotation_date', optional($quotation->quotation_date)->format('Y-m-d')) }}"
                                   class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('quotation_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Marketing</label>
                                <button type="button" id="openMarketingModal"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                    + Add Marketing
                                </button>
                            </div>
                            <input type="hidden" name="marketing_id" id="marketingId" value="{{ old('marketing_id', $quotation->marketing_id) }}">
                            <div class="relative">
                                <button type="button" id="marketingTrigger"
                                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 px-3 py-2.5 text-left text-sm text-slate-700">
                                    <span id="marketingLabel">Select marketing</span>
                                    <span class="text-slate-400">v</span>
                                </button>
                                <div id="marketingPanel" class="absolute z-40 mt-2 hidden w-full rounded-xl border border-slate-200 bg-white shadow-lg">
                                    <div class="border-b border-slate-100 p-2">
                                        <input type="text" id="marketingSearch" placeholder="Search marketing..."
                                               class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <ul id="marketingOptions" class="max-h-56 overflow-y-auto py-1 text-sm"></ul>
                                </div>
                            </div>
                            @error('marketing_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Revision No</label>
                            <input type="number" min="0" name="revision_no" value="{{ old('revision_no', $quotation->revision_no ?? 0) }}"
                                   class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('revision_no')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label class="text-sm font-medium text-slate-700">Customer Name</label>
                                <button type="button" id="openCustomerModal"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                    + Add Customer
                                </button>
                            </div>
                            <input type="hidden" name="company_id" id="customerId" value="{{ old('company_id', $quotation->company_id) }}">
                            <div class="relative">
                                <button type="button" id="customerTrigger"
                                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 px-3 py-2.5 text-left text-sm text-slate-700">
                                    <span id="customerLabel">Select customer</span>
                                    <span class="text-slate-400">v</span>
                                </button>
                                <div id="customerPanel" class="absolute z-40 mt-2 hidden w-full rounded-xl border border-slate-200 bg-white shadow-lg">
                                    <div class="border-b border-slate-100 p-2">
                                        <input type="text" id="customerSearch" placeholder="Search customer..."
                                               class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <ul id="customerOptions" class="max-h-56 overflow-y-auto py-1 text-sm"></ul>
                                </div>
                            </div>
                            @error('company_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Address</label>
                            <textarea name="company_address" id="companyAddress" rows="4"
                                      class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('company_address', $quotation->company_address) }}</textarea>
                            @error('company_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Attention</label>
                            <input type="text" name="attention" value="{{ old('attention', $quotation->attention) }}"
                                   class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Delivery To</label>
                            <input type="text" name="delivery_to" value="{{ old('delivery_to', $quotation->delivery_to) }}"
                                   class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Delivery Term</label>
                            <input type="text" name="delivery_term" value="{{ old('delivery_term', $quotation->delivery_term) }}"
                                   class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-[120px,1fr] gap-3 items-center">
                            <input type="number" min="0" name="payment_days" value="{{ old('payment_days', $quotation->payment_days) }}"
                                   class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">days after delivery & invoice</span>
                        </div>

                        <div class="grid grid-cols-[120px,1fr] gap-3 items-center">
                            <input type="number" min="0" name="delivery_time_days" value="{{ old('delivery_time_days', $quotation->delivery_time_days) }}"
                                   class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">days after PO received</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Scope of Work</label>
                            <select name="scope_of_work"
                                    class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach (['Supply Only', 'Supply + Installation', 'Installation Only'] as $scope)
                                    <option value="{{ $scope }}" @selected(old('scope_of_work', $quotation->scope_of_work) === $scope)>{{ $scope }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-[120px,1fr] gap-3 items-center">
                            <input type="number" min="0" name="price_validity_weeks" value="{{ old('price_validity_weeks', $quotation->price_validity_weeks) }}"
                                   class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="text-sm text-slate-700">weeks after quotation date</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('settings.quotations.index') }}"
                       class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">
                        Close
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Update and Close
                    </button>
                </div>
            </form>
        </div>
    </div>

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
            const customerData = @json($customerOptions);
            const marketingData = @json($marketingOptions);

            function toggleModal(id, show) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.toggle('hidden', !show);
                modal.classList.toggle('flex', show);
            }

            function createCombobox(config) {
                const trigger = document.getElementById(config.triggerId);
                const panel = document.getElementById(config.panelId);
                const search = document.getElementById(config.searchId);
                const optionsEl = document.getElementById(config.optionsId);
                const hiddenInput = document.getElementById(config.hiddenId);
                const labelEl = document.getElementById(config.labelId);

                let items = config.items;
                let selectedId = hiddenInput.value ? Number(hiddenInput.value) : null;

                function setSelected(item) {
                    selectedId = Number(item.id);
                    hiddenInput.value = String(item.id);
                    labelEl.textContent = item.label;
                    if (typeof config.onSelect === 'function') {
                        config.onSelect(item);
                    }
                }

                function render(filterText) {
                    const keyword = (filterText || '').toLowerCase().trim();
                    const filtered = items.filter(item => item.label.toLowerCase().includes(keyword));
                    optionsEl.innerHTML = '';

                    if (!filtered.length) {
                        optionsEl.innerHTML = '<li class="px-3 py-2 text-slate-400">No data found</li>';
                        return;
                    }

                    filtered.forEach(item => {
                        const li = document.createElement('li');
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full px-3 py-2 text-left text-slate-700 hover:bg-slate-50';
                        btn.textContent = item.label;
                        btn.addEventListener('click', function () {
                            setSelected(item);
                            panel.classList.add('hidden');
                        });
                        li.appendChild(btn);
                        optionsEl.appendChild(li);
                    });
                }

                trigger.addEventListener('click', function () {
                    panel.classList.toggle('hidden');
                    if (!panel.classList.contains('hidden')) search.focus();
                });

                search.addEventListener('input', function () {
                    render(this.value);
                });

                document.addEventListener('click', function (event) {
                    if (!panel.contains(event.target) && !trigger.contains(event.target)) {
                        panel.classList.add('hidden');
                    }
                });

                const initial = items.find(item => item.id === selectedId);
                if (initial) {
                    labelEl.textContent = initial.label;
                    if (typeof config.onSelect === 'function') {
                        config.onSelect(initial, true);
                    }
                }

                render('');

                return {
                    addItem: function (item, selectNow) {
                        items.push(item);
                        if (selectNow) setSelected(item);
                        render(search.value);
                    }
                };
            }

            async function postInline(formId, url, errorId, onSuccess) {
                const form = document.getElementById(formId);
                const error = document.getElementById(errorId);

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

            const companyAddress = document.getElementById('companyAddress');

            const customerCombobox = createCombobox({
                triggerId: 'customerTrigger',
                panelId: 'customerPanel',
                searchId: 'customerSearch',
                optionsId: 'customerOptions',
                hiddenId: 'customerId',
                labelId: 'customerLabel',
                items: customerData,
                onSelect: function (item) {
                    if (companyAddress && item.address) {
                        companyAddress.value = item.address;
                    }
                }
            });

            const marketingCombobox = createCombobox({
                triggerId: 'marketingTrigger',
                panelId: 'marketingPanel',
                searchId: 'marketingSearch',
                optionsId: 'marketingOptions',
                hiddenId: 'marketingId',
                labelId: 'marketingLabel',
                items: marketingData,
            });

            document.getElementById('openCustomerModal').addEventListener('click', function () {
                toggleModal('customerModal', true);
            });
            document.getElementById('openMarketingModal').addEventListener('click', function () {
                toggleModal('marketingModal', true);
            });
            document.querySelectorAll('[data-close-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    toggleModal(this.getAttribute('data-close-modal'), false);
                });
            });

            postInline(
                'customerInlineForm',
                "{{ route('settings.quotations.inline-companies.store') }}",
                'customerInlineError',
                function (data) {
                    customerCombobox.addItem({ id: Number(data.id), label: data.label, address: data.address || '' }, true);
                    toggleModal('customerModal', false);
                }
            );

            postInline(
                'marketingInlineForm',
                "{{ route('settings.quotations.inline-marketings.store') }}",
                'marketingInlineError',
                function (data) {
                    marketingCombobox.addItem({ id: Number(data.id), label: data.label }, true);
                    toggleModal('marketingModal', false);
                }
            );
        })();
    </script>
</x-app-layout>
