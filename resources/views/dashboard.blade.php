<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Dashboard Overview</h2>
            <p class="text-sm text-gray-600">
                Ringkasan performa sistem dan shortcut pekerjaan harian.
            </p>
        </div>
    </x-slot>

    @php
        $userRole = auth()->user()->getRoleNames()->first() ?? 'user';
        $stats = [
            [
                'label' => 'Total Quotation',
                'value' => $totalQtn ?? 0,
                'icon' => 'request_quote',
                'note' => 'Dokumen quotation aktif di sistem',
                'accent' => 'from-indigo-600 to-indigo-500',
            ],
            [
                'label' => 'Total Order',
                'value' => $totalOrder ?? 0,
                'icon' => 'list_alt',
                'note' => 'Order list yang sudah tercatat',
                'accent' => 'from-emerald-600 to-emerald-500',
            ],
            [
                'label' => 'Total Customer',
                'value' => $totalCustomer ?? 0,
                'icon' => 'group',
                'note' => 'Data customer terdaftar',
                'accent' => 'from-amber-500 to-orange-500',
            ],
        ];
    @endphp

    <div class="w-full px-6 lg:px-10 py-8 space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-slate-900 text-white shadow-xl">
            <div class="absolute -top-24 -right-16 h-64 w-64 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <div class="absolute -bottom-28 left-0 h-64 w-64 rounded-full bg-cyan-500/20 blur-3xl"></div>

            <div class="relative px-6 py-8 lg:px-8 lg:py-9 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-200">Welcome Back</p>
                    <h3 class="mt-2 text-2xl lg:text-3xl font-bold">
                        Hi, {{ auth()->user()->name }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-200 max-w-xl">
                        Kamu login sebagai <span class="font-semibold uppercase">{{ $userRole }}</span>.
                        Pantau status data dan lanjutkan pekerjaan langsung dari dashboard.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-2xl bg-white/10 px-4 py-3 border border-white/10">
                        <p class="text-xs text-slate-200">Role</p>
                        <p class="mt-1 text-sm font-semibold uppercase">{{ $userRole }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3 border border-white/10">
                        <p class="text-xs text-slate-200">Status</p>
                        <p class="mt-1 text-sm font-semibold text-emerald-300">Online</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3 border border-white/10">
                        <p class="text-xs text-slate-200">Date</p>
                        <p class="mt-1 text-sm font-semibold">{{ now()->format('d M Y') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3 border border-white/10">
                        <p class="text-xs text-slate-200">Time</p>
                        <p class="mt-1 text-sm font-semibold">{{ now()->format('H:i') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($stats as $stat)
                <article class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $stat['accent'] }}"></div>
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                            <h4 class="mt-2 text-4xl font-bold text-slate-900">{{ $stat['value'] }}</h4>
                            <p class="mt-1 text-xs text-slate-500">{{ $stat['note'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <span class="material-symbols-rounded text-[24px] text-slate-700">{{ $stat['icon'] }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Updated</span>
                        <span class="font-semibold text-slate-700">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Quick Actions</h3>
                    <p class="text-xs text-slate-500">Akses cepat menu utama</p>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @role('admin|estimator')
                        <a href="{{ route('calculation.rti') }}" class="rounded-xl border border-slate-200 p-4 hover:border-indigo-300 hover:bg-indigo-50/40 transition">
                            <p class="text-sm font-semibold text-slate-900">Calculation RTI</p>
                            <p class="text-xs text-slate-500 mt-1">Mulai perhitungan produk RTI</p>
                        </a>
                        <a href="{{ route('calculation.gpp') }}" class="rounded-xl border border-slate-200 p-4 hover:border-indigo-300 hover:bg-indigo-50/40 transition">
                            <p class="text-sm font-semibold text-slate-900">Calculation GPP</p>
                            <p class="text-xs text-slate-500 mt-1">Mulai perhitungan produk GPP</p>
                        </a>
                        <a href="{{ route('calculation.ejm') }}" class="rounded-xl border border-slate-200 p-4 hover:border-indigo-300 hover:bg-indigo-50/40 transition">
                            <p class="text-sm font-semibold text-slate-900">Calculation EJM</p>
                            <p class="text-xs text-slate-500 mt-1">Mulai perhitungan produk EJM</p>
                        </a>
                    @endrole

                    @role('admin|ppc|logistik')
                        <a href="{{ route('extractor.rti') }}" class="rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
                            <p class="text-sm font-semibold text-slate-900">Extractor RTI</p>
                            <p class="text-xs text-slate-500 mt-1">Tarik data extractor RTI</p>
                        </a>
                        <a href="{{ route('extractor.gpp') }}" class="rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
                            <p class="text-sm font-semibold text-slate-900">Extractor GPP</p>
                            <p class="text-xs text-slate-500 mt-1">Tarik data extractor GPP</p>
                        </a>
                        <a href="{{ route('extractor.ejm') }}" class="rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
                            <p class="text-sm font-semibold text-slate-900">Extractor EJM</p>
                            <p class="text-xs text-slate-500 mt-1">Tarik data extractor EJM</p>
                        </a>
                    @endrole

                    @role('admin')
                        <a href="{{ route('setting') }}" class="rounded-xl border border-slate-200 p-4 hover:border-slate-400 hover:bg-slate-50 transition md:col-span-2 lg:col-span-3">
                            <p class="text-sm font-semibold text-slate-900">Settings Center</p>
                            <p class="text-xs text-slate-500 mt-1">Kelola role access, marketing, dan customer dari satu menu.</p>
                        </a>
                    @endrole
                </div>
            </div>

            <div class="xl:col-span-4 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Recent Activity</h3>
                    <p class="text-xs text-slate-500">Log ringkas aktivitas hari ini</p>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Dashboard diakses</p>
                            <p class="text-xs text-slate-500">{{ now()->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Role user aktif tervalidasi</p>
                            <p class="text-xs text-slate-500">Role: {{ strtoupper($userRole) }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Sinkronisasi data terakhir</p>
                            <p class="text-xs text-slate-500">{{ now()->subMinutes(12)->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
