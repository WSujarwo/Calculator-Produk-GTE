<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Settings</h2>
            <p class="text-sm text-gray-600">Kelola seluruh konfigurasi master, user access, dan data relasi dalam satu tempat.</p>
        </div>
    </x-slot>

    <div class="w-full px-6 lg:px-10 py-8">
        <div class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-6 lg:p-8 text-white shadow-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-200">System Configuration Center</p>
                    <h3 class="mt-2 text-2xl lg:text-3xl font-bold">Application Settings</h3>
                    <p class="mt-2 text-sm text-slate-200 max-w-2xl">
                        Area ini digunakan untuk mengatur akses role, daftar marketing, dan data customer agar operasional lebih rapi.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-2xl bg-white/10 px-4 py-3">
                        <p class="text-xs text-slate-200">Modules</p>
                        <p class="text-xl font-bold">5</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3">
                        <p class="text-xs text-slate-200">Status</p>
                        <p class="text-sm font-semibold">Active</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3">
                        <p class="text-xs text-slate-200">Access</p>
                        <p class="text-sm font-semibold">Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-3 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                <h4 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Setting Groups</h4>
                <div class="mt-4 space-y-2">
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2">
                        <p class="text-sm font-semibold text-indigo-700">Master Data</p>
                        <p class="text-xs text-indigo-600">Marketing dan Customer</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="text-sm font-semibold text-slate-700">Authorization</p>
                        <p class="text-xs text-slate-600">Role & Permission</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="text-sm font-semibold text-slate-700">Maintenance</p>
                        <p class="text-xs text-slate-600">Konfigurasi lanjutan</p>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-9 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                <section class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        M
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-slate-900">Marketing</h4>
                    <p class="mt-1 text-sm text-slate-600 min-h-12">
                        Kelola data marketing, tambah akun baru, edit informasi, dan hapus data.
                    </p>
                    <a href="{{ route('settings.marketings.index') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Open Module
                    </a>
                </section>

                <section class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        C
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-slate-900">Perusahaan / Customer </h4>
                    <p class="mt-1 text-sm text-slate-600 min-h-12">
                        Kelola data Perusahaan/Customer, tambah akun baru, edit informasi, dan hapus data.
                    </p>
                    <a href="{{ route('settings.companies.index') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Open Module
                    </a>
                </section>

                <section class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        R
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-slate-900">Role Access</h4>
                    <p class="mt-1 text-sm text-slate-600 min-h-12">
                        Atur permission per role dan mapping role user berdasarkan kebutuhan.
                    </p>
                    <a href="{{ route('setting.role-access') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Open Module
                    </a>
                </section>

                <section class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="h-10 w-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold">
                        G
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-slate-900">Validasi Data GPP</h4>
                    <p class="mt-1 text-sm text-slate-600 min-h-12">
                        Lihat daftar tabel validasi GPP yang sudah masuk database dan preview data per tabel.
                    </p>
                    <a href="{{ route('setting.gpp-validation') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">
                        Open Module
                    </a>
                </section>

                <section class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        E
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-slate-900">Validasi Data EJM</h4>
                    <p class="mt-1 text-sm text-slate-600 min-h-12">
                        Kelola data validasi EJM termasuk create/edit manual dan import CSV/XLSX berbasis NB.
                    </p>
                    <a href="{{ route('setting.ejm-validation.index') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        Open Module
                    </a>
                </section>

            </div>
        </div>
    </div>
</x-app-layout>
