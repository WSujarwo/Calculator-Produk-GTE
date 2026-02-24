<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Role Access Management</h2>
            <p class="text-sm text-gray-600">Atur privilege role per modul dengan checklist lalu simpan.</p>
        </div>
    </x-slot>

    @php
        $groupedRows = collect($permissionRows)->groupBy('group_label');
    @endphp

    <div class="w-full px-6 lg:px-10 py-6 space-y-6 text-gray-900">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <div class="font-semibold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Privilege Per Role</h3>
                    <p class="text-sm text-gray-600">Pilih role, checklist privilege, lalu simpan.</p>
                </div>

                <form method="GET" action="{{ route('setting.role-access') }}" class="flex items-center gap-2">
                    <label for="role" class="text-sm text-gray-600">Role</label>
                    <select id="role" name="role" onchange="this.form.submit()"
                            class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(optional($selectedRole)->name === $role->name)>
                                {{ strtoupper($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($selectedRole)
                <form method="POST" action="{{ route('setting.role-access.roles.update', $selectedRole) }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <input id="moduleSearch" type="text" placeholder="Cari modul..."
                               class="w-full md:w-80 rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900" />

                        <div class="flex flex-wrap items-center gap-4">
                            @foreach($actionLabels as $action => $label)
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                                    <input type="checkbox"
                                           data-action="{{ $action }}"
                                           class="action-toggle rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    {{ $label }} (semua)
                                </label>
                            @endforeach
                        </div>

                        <button type="submit"
                                class="inline-flex justify-center items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Simpan Permission {{ strtoupper($selectedRole->name) }}
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($groupedRows as $groupLabel => $rows)
                            <details open class="permission-group rounded-xl border border-gray-200 overflow-hidden">
                                <summary class="cursor-pointer list-none bg-gray-50 px-4 py-3 flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">{{ $groupLabel }}</span>
                                    <span class="text-xs text-gray-600">{{ $rows->count() }} modul</span>
                                </summary>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-white text-gray-700 border-y border-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-semibold">Modul</th>
                                                @foreach($actionLabels as $label)
                                                    <th class="px-3 py-2 text-center font-semibold">{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($rows as $row)
                                                <tr class="permission-row" data-module="{{ strtolower($row['module_label']) }}">
                                                    <td class="px-3 py-3 font-semibold">{{ $row['module_label'] }}</td>
                                                    @foreach($row['permissions'] as $action => $permissionName)
                                                        <td class="px-3 py-3 text-center">
                                                            <input type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $permissionName }}"
                                                                   data-action-cell="{{ $action }}"
                                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                                   @checked($selectedRole->hasPermissionTo($permissionName))>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </form>
            @else
                <div class="p-5 text-sm text-gray-600">Belum ada role yang tersedia.</div>
            @endif
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-lg">Role Per User</h3>
                <p class="text-sm text-gray-600">Tentukan user masuk role apa (1 user = 1 role).</p>
            </div>

            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">User</th>
                            <th class="px-3 py-2 text-left font-semibold">Email</th>
                            <th class="px-3 py-2 text-left font-semibold">Role</th>
                            <th class="px-3 py-2 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-3 py-3 font-semibold">{{ $user->name }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $user->email }}</td>
                                <td class="px-3 py-3">
                                    <form method="POST" action="{{ route('setting.role-access.users.update', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role_name"
                                                class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-300 text-gray-900">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                                    {{ strtoupper($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="inline-flex justify-center items-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                            Update Role
                                        </button>
                                    </form>
                                </td>
                                <td class="px-3 py-3 text-gray-500">-</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('moduleSearch');
            const rows = Array.from(document.querySelectorAll('.permission-row'));
            const groups = Array.from(document.querySelectorAll('.permission-group'));
            const actionToggles = document.querySelectorAll('.action-toggle');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const keyword = searchInput.value.toLowerCase().trim();

                    rows.forEach(function (row) {
                        const moduleText = row.dataset.module || '';
                        row.style.display = moduleText.includes(keyword) ? '' : 'none';
                    });

                    groups.forEach(function (group) {
                        const visibleRows = group.querySelectorAll('.permission-row:not([style*="display: none"])');
                        group.style.display = visibleRows.length ? '' : 'none';
                    });
                });
            }

            actionToggles.forEach(function (toggle) {
                toggle.addEventListener('change', function () {
                    const action = toggle.dataset.action;
                    const cells = document.querySelectorAll('[data-action-cell="' + action + '"]');

                    cells.forEach(function (checkbox) {
                        checkbox.checked = toggle.checked;
                    });
                });
            });
        });
    </script>
</x-app-layout>
