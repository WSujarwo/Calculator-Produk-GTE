<x-app-layout>
    <x-slot name="header">
        <div class="px-6 lg:px-10">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Detail PCE</h2>
            <p class="text-sm text-gray-600">{{ $pceHeader->pce_number }}</p>
        </div>
    </x-slot>

    <div class="w-full px-5 lg:px-8 py-6 text-[#111]">
        <style>
            .pce-sheet { background:#efefef; border:1px solid #9aa3ad; padding:12px; }
            .pce-title { background:#334155; color:#fff; text-align:center; font-weight:700; letter-spacing:.3px; font-size:18px; padding:6px 10px; }
            .pce-meta { width:100%; border-collapse:collapse; font-size:12px; margin-top:8px; }
            .pce-meta td { padding:1px 4px; vertical-align:top; font-weight:700; }
            .pce-meta .val { font-weight:600; }
            .top-grid { display:grid; grid-template-columns: 180px 180px 1fr 1fr; gap:16px; margin-top:10px; align-items:start; }
            .box { border:1px solid #5f6b76; background:#fff; }
            .box-title { background:#334155; color:#fff; font-weight:700; font-size:12px; text-align:center; padding:6px 8px; }
            .box-body { padding:8px 10px; font-size:11px; min-height:76px; }
            .tiny { width:100%; border-collapse:collapse; font-size:11px; }
            .tiny th, .tiny td { border:1px solid #9ca3af; padding:3px 4px; text-align:center; font-weight:700; }
            .tiny th { background:#334155; color:#fff; }
            .tiny td { background:#e7f0df; }
            .placeholder { border:1px solid #1d4ed8; height:206px; background:#fff; }
            .cost { width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; }
            .cost th, .cost td { border:1px solid #a3a3a3; padding:2px 4px; vertical-align:top; }
            .h1 { background:#2f4f1f; color:#fff; font-weight:700; text-align:center; }
            .h2 { background:#2f4f1f; color:#fff; font-weight:700; text-align:center; }
            .sub { background:#c8ced6; font-weight:700; text-align:center; }
            .green { background:#cfe3c1; font-weight:700; text-align:center; }
            .yellow { background:#ffef5e; }
            .right { text-align:right; }
            .center { text-align:center; }
            .bold { font-weight:700; }
            .totalbar { background:#2f4f1f; color:#fff; font-weight:700; }
            .darkbar { background:#334155; color:#fff; font-weight:700; }
        </style>

        <div class="pce-sheet">
            <div class="pce-title">PROJECT COST ESTIMATION FOR METAL EXPANSION JOINT</div>

            <table class="pce-meta">
                <tr>
                    <td width="140">NAME OF PROJECT</td><td width="10">:</td><td class="val">{{ $pceHeader->project_name ?? 'Expansion Joint Metal' }}</td>
                    <td width="140">DOCUMENT NO</td><td width="10">:</td><td class="val">{{ $pceHeader->document_no ?? 'PCE-GTE-0920-000' }}</td>
                </tr>
                <tr>
                    <td>END USER ID</td><td>:</td><td class="val">{{ $pceHeader->company?->company_name ?? 'PT. AQPA INDONESIA' }}</td>
                    <td>REVISION</td><td>:</td><td class="val">{{ $pceHeader->revision ?? '4' }}</td>
                </tr>
                <tr>
                    <td>DRAWING NO</td><td>:</td><td class="val">{{ $pceHeader->drawing_no ?? '-' }}</td>
                    <td>DATE</td><td>:</td><td class="val">{{ optional($pceHeader->pce_date)->format('d-M-y') ?? '10-Sep-25' }}</td>
                </tr>
                <tr>
                    <td>AREA</td><td>:</td><td class="val">{{ $pceHeader->area ?? '-' }}</td>
                    <td>SALES</td><td>:</td><td class="val">{{ $pceHeader->marketing?->name ?? 'Marketing' }}</td>
                </tr>
            </table>

            <div class="top-grid">
                <div class="box">
                    <div class="box-title">EJM SHAPE</div>
                    <div class="box-body">
                        <label><input type="radio" disabled> Round</label>
                        <label style="margin-left:14px;"><input type="radio" disabled> Square</label>
                    </div>
                </div>

                <div class="box">
                    <div class="box-title">ADDITIONAL PART</div>
                    <div class="box-body">
                        <div><input type="checkbox" disabled> Bellows + Collar</div>
                        <div><input type="checkbox" disabled> Pipe End</div>
                        <div><input type="checkbox" disabled> Sleeve</div>
                        <div><input type="checkbox" disabled> Flange</div>
                    </div>
                </div>

                <div>
                    <table class="tiny">
                        <tr><th colspan="4">Bellows+Collar</th></tr>
                        <tr><th>OAL</th><th>QTY NOC</th><th>Material</th><th>Can Length</th></tr>
                        <tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>
                    </table>
                    <table class="tiny" style="margin-top:8px;">
                        <tr><th colspan="3">Sleeve</th></tr>
                        <tr><th>Material</th><th>Thk</th><th>Length</th></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                    </table>
                </div>

                <div>
                    <table class="tiny">
                        <tr><th colspan="3">Pipe End</th></tr>
                        <tr><th>Material</th><th>LPE</th><th>QTY</th></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                    </table>
                    <table class="tiny" style="margin-top:8px;">
                        <tr><th colspan="3">Flange</th></tr>
                        <tr><th>Material</th><th>Class</th><th>Type</th></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                    </table>
                    <div class="placeholder" style="margin-top:8px;"></div>
                </div>
            </div>

            <table class="cost">
                <thead>
                    <tr>
                        <th class="h1" rowspan="2" width="36">NO</th>
                        <th class="h1" rowspan="2" width="390">JOB DESCRIPTION</th>
                        <th class="h1" colspan="3">SPECIFICATION</th>
                        <th class="h1" rowspan="2" width="78">QUANTITY</th>
                        <th class="h1" colspan="2">MATERIAL COST</th>
                        <th class="h1" colspan="2">LABOUR COST</th>
                    </tr>
                    <tr>
                        <th class="h2" width="190">DESCRIPTION</th>
                        <th class="h2" width="190">MATERIAL</th>
                        <th class="h2" width="80">WEIGHT</th>
                        <th class="h2" width="95">UNIT</th>
                        <th class="h2" width="130">TOTAL</th>
                        <th class="h2" width="95">UNIT</th>
                        <th class="h2" width="130">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center bold">1</td>
                        <td>
                            <div class="bold">ENGINEERING</div>
                            <div style="padding-left:28px;" class="bold">1,1 &nbsp; SURVEY</div>
                            <div style="padding-left:58px;">1.1.1 &nbsp; SURVEY PACKAGE</div>
                            <div style="padding-left:28px;margin-top:4px;" class="bold">1,2 &nbsp; DESIGN</div>
                            <div style="padding-left:58px;">2.1.1 &nbsp; DETAIL DRAWING</div>
                            <div style="padding-left:58px;">2.1.2 &nbsp; DESIGN CALCULATION</div>
                        </td>
                        <td style="vertical-align:bottom;">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center" style="vertical-align:bottom;">-</td>
                        <td class="right" style="vertical-align:bottom;">-</td>
                        <td class="right" style="vertical-align:bottom;">-</td>
                        <td class="right" style="vertical-align:bottom;">-</td>
                        <td class="right" style="vertical-align:bottom;">-</td>
                    </tr>
                    <tr class="sub"><td colspan="8">SUBTOTAL 1</td><td colspan="2">-</td></tr>

                    <tr>
                        <td class="center bold">2</td>
                        <td>
                            <div class="bold">MATERIALS</div>
                            <div style="padding-left:28px;" class="bold">2,1 &nbsp; RAW MATERIALS</div>
                            <div style="padding-left:58px;">2.1.1 &nbsp; BELLOWS + COLLAR</div>
                            <div style="padding-left:58px;">2.1.2 &nbsp; PIPE END</div>
                            <div style="padding-left:58px;">2.1.3 &nbsp; FLANGE</div>
                            <div style="padding-left:58px;">2.1.4 &nbsp; SLEEVE 1 & SLEEVE 2</div>
                            <div style="padding-left:58px;">2.1.5 &nbsp; HOLDER</div>
                            <div style="padding-left:58px;">2.1.6 &nbsp; TIE ROD, NUT, WASHER</div>
                            <div style="padding-left:28px;margin-top:8px;" class="bold">2,2 &nbsp; CONSUMABLES</div>
                            <div style="padding-left:58px;">3.2.0 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.1 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.2 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.3 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.4 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.5 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.6 &nbsp; WELDING ROD</div>
                            <div style="padding-left:58px;">3.2.7 &nbsp; CUTTING WHEEL 4"</div>
                            <div style="padding-left:58px;">3.2.8 &nbsp; FLESIBLE DISC 4"</div>
                            <div style="padding-left:58px;">3.2.9 &nbsp; BUFFING DISC 4"</div>
                            <div style="padding-left:58px;">3.2.10 &nbsp; ANTI SPATER</div>
                            <div style="padding-left:58px;">3.2.11 &nbsp; KOAS 1"</div>
                            <div style="padding-left:58px;">3.2.12 &nbsp; PRIMER COAT</div>
                            <div style="padding-left:58px;">3.2.13 &nbsp; THINER</div>
                            <div style="padding-left:58px;">3.2.14 &nbsp; SECOND COAT</div>
                            <div style="padding-left:58px;">3.2.15 &nbsp; THINER</div>
                            <div style="padding-left:58px;">3.2.16 &nbsp; AUTO SOL</div>
                            <div style="padding-left:58px;">3.2.17 &nbsp; NDT Test - Magnaflux SKC-S ( Remover )</div>
                            <div style="padding-left:58px;">3.2.18 &nbsp; NDT Test - Magnaflux SKD-S2 ( Developer )</div>
                            <div style="padding-left:58px;">3.2.19 &nbsp; NDT Test - Magnaflux SKL-SP2 ( Penetrant )</div>
                        </td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                    </tr>
                    <tr class="sub"><td colspan="8">SUBTOTAL 2</td><td colspan="2">-</td></tr>

                    <tr>
                        <td class="center bold">3</td>
                        <td>
                            <div class="bold">FABRICATION</div>
                            <div style="padding-left:28px;" class="bold">3,1 &nbsp; PROCESS</div>
                            <div style="padding-left:58px;">3.1.1 &nbsp; Cutting</div>
                            <div style="padding-left:58px;">3.1.2 &nbsp; Drilling</div>
                            <div style="padding-left:58px;">3.1.3 &nbsp; Welding</div>
                            <div style="padding-left:58px;">3.1.5 &nbsp; Assembly</div>
                            <div style="padding-left:58px;">3.1.6 &nbsp; Painting</div>
                            <div style="padding-left:58px;">3.1.7 &nbsp; Finishing</div>
                            <div style="padding-left:58px;">3.1.8 &nbsp; Milling</div>
                            <div style="padding-left:58px;">3.1.9 &nbsp; Turning</div>
                            <div style="padding-left:58px;">3.1.10 &nbsp; Bellows</div>
                        </td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                    </tr>
                    <tr class="sub"><td colspan="8">SUBTOTAL 3</td><td colspan="2">-</td></tr>

                    <tr>
                        <td class="center bold">4</td>
                        <td>
                            <div class="bold">QUALITY CONTROL</div>
                            <div style="padding-left:28px;">4,1 &nbsp; VISUAL INSPECTION</div>
                            <div style="padding-left:28px;">4,2 &nbsp; DIMENSIONAL INSPECTION</div>
                            <div style="padding-left:28px;">4,3 &nbsp; PENETRANT TEST</div>
                            <div style="padding-left:28px;">4,4 &nbsp; LEAK TEST</div>
                        </td>
                        <td class="center">-</td><td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                    </tr>
                    <tr class="sub"><td colspan="8">SUBTOTAL 4</td><td colspan="2">-</td></tr>

                    <tr>
                        <td class="center bold">5</td>
                        <td>
                            <div class="bold">PACKAGING & DELIVERY</div>
                            <div style="padding-left:28px;">5,1 &nbsp; PACKAGING</div>
                            <div style="padding-left:28px;">5,2 &nbsp; DELIVERY</div>
                        </td>
                        <td class="center">-</td><td class="center">-</td><td class="center">-</td>
                        <td class="center">-</td>
                        <td class="right">-</td><td class="right">-</td><td class="right">-</td><td class="right">-</td>
                    </tr>
                    <tr class="sub"><td colspan="8">SUBTOTAL 5</td><td colspan="2">-</td></tr>

                    <tr class="totalbar">
                        <td colspan="8" class="center">TOTAL ( 1 + 2 + 3 + 4 + 5 )</td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                    </tr>
                    <tr class="darkbar"><td colspan="8" class="right">TOTAL</td><td colspan="2" class="right">-</td></tr>
                    <tr class="darkbar"><td colspan="8" class="right">MARGIN</td><td class="center">-</td><td class="right">-</td></tr>
                    <tr class="darkbar"><td colspan="8" class="right">GRAND TOTAL</td><td colspan="2" class="right">-</td></tr>
                </tbody>
            </table>

            <div class="pt-2 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('pcelist') }}"
                   class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                    Kembali
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" disabled class="rounded-xl border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-500">Print</button>
                    <button type="button" disabled class="rounded-xl border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-500">Export PDF</button>
                    <button type="button" disabled class="rounded-xl border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-500">Export Excel</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
