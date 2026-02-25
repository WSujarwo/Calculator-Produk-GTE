<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-white leading-tight">Create Quotation</h2>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/40 p-8">
            <form action="{{ route('settings.quotations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Quotation Date</label>
                    <input type="date" name="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('quotation_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <label class="block text-sm font-medium text-gray-700">Company</label>
                        <button type="button" id="openCompanyModal"
                                class="px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            + Add Company
                        </button>
                    </div>
                    <input type="text" id="companySearch" placeholder="Search company..."
                           class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <select name="company_id" id="companySelect"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Select Company --</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                                {{ $company->company_code }} - {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <label class="block text-sm font-medium text-gray-700">Marketing</label>
                        <button type="button" id="openMarketingModal"
                                class="px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            + Add Marketing
                        </button>
                    </div>
                    <input type="text" id="marketingSearch" placeholder="Search marketing..."
                           class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <select name="marketing_id" id="marketingSelect"
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Select Marketing --</option>
                        @foreach ($marketings as $marketing)
                            <option value="{{ $marketing->id }}" @selected(old('marketing_id') == $marketing->id)>
                                {{ $marketing->marketing_no }} - {{ $marketing->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('marketing_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('settings.quotations.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">
                        Save Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="companyModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Company</h3>
                <button type="button" data-close-modal="companyModal" class="text-gray-500 hover:text-gray-700">x</button>
            </div>
            <form id="companyInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="company_code" placeholder="Company Code" class="w-full rounded-xl border-gray-300" required>
                <input type="text" name="company_name" placeholder="Company Name" class="w-full rounded-xl border-gray-300" required>
                <textarea name="address" placeholder="Address" rows="3" class="w-full rounded-xl border-gray-300"></textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-gray-300">
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-gray-300">
                </div>
                <p id="companyInlineError" class="text-sm text-red-600 hidden"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="companyModal" class="px-4 py-2 bg-gray-200 text-sm rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-xl">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="marketingModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Marketing</h3>
                <button type="button" data-close-modal="marketingModal" class="text-gray-500 hover:text-gray-700">x</button>
            </div>
            <form id="marketingInlineForm" class="space-y-3">
                @csrf
                <input type="text" name="marketing_no" placeholder="Marketing No" class="w-full rounded-xl border-gray-300" required>
                <input type="text" name="name" placeholder="Name" class="w-full rounded-xl border-gray-300" required>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="email" name="email" placeholder="Email" class="w-full rounded-xl border-gray-300" required>
                    <input type="text" name="phone" placeholder="Phone" class="w-full rounded-xl border-gray-300">
                </div>
                <p id="marketingInlineError" class="text-sm text-red-600 hidden"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-modal="marketingModal" class="px-4 py-2 bg-gray-200 text-sm rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-xl">Save</button>
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

            function setupSearch(searchId, selectId) {
                const search = document.getElementById(searchId);
                const select = document.getElementById(selectId);
                if (!search || !select) return;

                search.addEventListener('input', function () {
                    const keyword = this.value.toLowerCase().trim();
                    Array.from(select.options).forEach(function (option, index) {
                        if (index === 0) return;
                        const show = option.text.toLowerCase().includes(keyword);
                        option.hidden = !show;
                    });
                });
            }

            async function postInline(formId, url, errorId, onSuccess) {
                const form = document.getElementById(formId);
                const error = document.getElementById(errorId);
                if (!form) return;

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
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
                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to save data.');
                        }

                        onSuccess(data);
                        form.reset();
                    } catch (err) {
                        error.textContent = err.message;
                        error.classList.remove('hidden');
                    }
                });
            }

            document.getElementById('openCompanyModal')?.addEventListener('click', function () {
                toggleModal('companyModal', true);
            });
            document.getElementById('openMarketingModal')?.addEventListener('click', function () {
                toggleModal('marketingModal', true);
            });
            document.querySelectorAll('[data-close-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    toggleModal(this.getAttribute('data-close-modal'), false);
                });
            });

            setupSearch('companySearch', 'companySelect');
            setupSearch('marketingSearch', 'marketingSelect');

            postInline(
                'companyInlineForm',
                "{{ route('settings.quotations.inline-companies.store') }}",
                'companyInlineError',
                function (data) {
                    const select = document.getElementById('companySelect');
                    const option = new Option(data.label, data.id, true, true);
                    select.add(option);
                    toggleModal('companyModal', false);
                }
            );

            postInline(
                'marketingInlineForm',
                "{{ route('settings.quotations.inline-marketings.store') }}",
                'marketingInlineError',
                function (data) {
                    const select = document.getElementById('marketingSelect');
                    const option = new Option(data.label, data.id, true, true);
                    select.add(option);
                    toggleModal('marketingModal', false);
                }
            );
        })();
    </script>
</x-app-layout>
