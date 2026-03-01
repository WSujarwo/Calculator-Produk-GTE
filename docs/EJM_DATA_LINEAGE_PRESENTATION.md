# Dokumen Teknis Alur Data EJM

Dokumen ini menjelaskan sumber data, alur hitung, dan titik revisi pada modul EJM agar proses estimasi dapat diaudit end-to-end.

## 1. Tujuan

1. Menjelaskan setiap nilai di detail EJM diambil dari tabel mana.
2. Menjelaskan urutan generate dari item sampai grand total final.
3. Menentukan titik revisi jika ada perubahan data proses, material, atau rate.

## 2. Arsitektur Data

### 2.1 Data Transaksi (input user)

1. `pce_headers`
- Header order (nomor PCE, project, status).

2. `pce_items`
- Item detail per header (NB, NOC, qty, material pilihan, dsb).

### 2.2 Data Master Validasi / Rate

1. `data_validasi_ejm_expansion_joints`
- Master dimensi dan parameter expansion joint per NB.

2. `ejm_special_materials`
- Master material per komponen (bellow/collar/pipe/flange/dll), part number, harga.

3. `ejm_process_definitions`
- Master definisi proses per komponen (`Bellows`, `Collar`, `Pipe End`, `EJM Production`, dll).

4. `ejm_process_times`
- Waktu proses (menit) per proses, per NB/NOC (atau default NB null).

5. `cost_products`
- Master rate biaya (manpower, machine, consumable, grinding, dll).

### 2.3 Data Hasil Generate (per modul)

1. `ejm_detail_tubes`
2. `ejm_detail_bellows`
3. `ejm_detail_collars`
4. `ejm_detail_metal_bellows`
5. `ejm_detail_pipe_ends`
6. `ejm_detail_flanges`
7. `ejm_detail_ejms` (final aggregation + margin)

## 3. Alur Proses End-to-End

1. User membuat `PCE Header`.
2. User mengisi `PCE Item`.
3. Sistem mengaitkan item ke master validasi expansion joint berdasarkan NB.
4. User generate modul detail satu per satu:
- Detail Tube
- Detail Bellows
- Detail Collar
- Detail Metal Bellows
- Detail Pipe End
- Detail Flange
5. User generate `Detail EJM (Final)`.
6. Estimator mengisi margin manual.
7. Grand total dipakai di:
- `List Order` (per item)
- `List PCE Header` (akumulasi per header)

## 4. Data Lineage Per Modul

## 4.1 Detail Tube (`ejm_detail_tubes`)

### Sumber

1. `pce_items` + relasi `validation` (`data_validasi_ejm_expansion_joints`)
2. `ejm_process_definitions` + `ejm_process_times`
3. `ejm_special_materials` (material bellow / pipe end)
4. `cost_products` (fallback rate)

### Proses yang dibaca

1. `rolling`
2. `seam_welding`
3. `welding`

### Rumus geometri utama

1. `BL = NOC * Pitch`
2. `OALB = (2 * LC) + BL`
3. `OAL = OALB + (2 * LPE) + (2 * LC) + (2 * GPF)`

### Output utama

1. `harga_material`
2. `total`

## 4.2 Detail Bellows (`ejm_detail_bellows`)

### Sumber

1. `pce_items` + `data_validasi_ejm_expansion_joints`
2. `ejm_process_definitions(component_type='Bellows')`
3. `ejm_process_times` (minutes inner/outer)
4. `ejm_special_materials` (material bellow)
5. `cost_products`:
- `EJM_MACHINE_MINUTE`
- `EJM_MANPOWER_HOUR`

### Proses wajib

1. `Cutting Shearing`
2. `Rolling`
3. `Seam Welding`
4. `Hydro Forming`

### Output utama

1. `cost_raw_material`
2. `machine_cost`
3. `total_cost_manpower`
4. `total_price`

## 4.3 Detail Collar (`ejm_detail_collars`)

### Sumber

1. `pce_items` + `data_validasi_ejm_expansion_joints`
2. `ejm_process_definitions(component_type='Collar')`
3. `ejm_process_times`
4. `ejm_special_materials` komponen `COLLAR`
5. `cost_products`:
- `EJM_MACHINE_MINUTE`
- `EJM_MANPOWER_HOUR`

### Proses wajib

1. `Cutting Shearing`
2. `Rolling`
3. `Welding`

### Output utama

1. `cost_raw_material`
2. `cost_machine_material`
3. `total_cost_manpower`
4. `total_price`

## 4.4 Detail Metal Bellows (`ejm_detail_metal_bellows`)

### Sumber

1. `pce_items` + `data_validasi_ejm_expansion_joints`
2. `ejm_special_materials`:
- material bellow
- material collar (lookup component `COLLAR`)
3. `cost_products`:
- `EJM_METAL_WELDING_ROD`
- `EJM_METAL_MESIN`
- `EJM_METAL_GRINDA_POLES`
- `EJM_METAL_DISC_POLES`
- `EJM_METAL_MANPOWER` (opsional)
- `EJM_MANPOWER_HOUR` (fallback manpower)

### Catatan manpower

1. Prioritas: `EJM_METAL_MANPOWER`.
2. Jika kosong: fallback ke `EJM_MANPOWER_HOUR / 60` (per menit).

### Output utama

1. `total`
2. `grand_total` (dikali qty item)

## 4.5 Detail Pipe End (`ejm_detail_pipe_ends`)

### Sumber

1. `pce_items` + `data_validasi_ejm_expansion_joints`
2. `ejm_process_definitions(component_type='Pipe End')`
3. `ejm_process_times`
4. `ejm_special_materials` (pipe end)
5. `cost_products`:
- `EJM_MACHINE_MINUTE`
- `EJM_MANPOWER_HOUR`

### Proses wajib

1. `Cutting`
2. `Bevel`
3. `Grinding`

### Output utama

1. `cost_raw_material`
2. `cost_machine`
3. `total_cost`
4. `total_price`

## 4.6 Detail Flange (`ejm_detail_flanges`)

### Sumber

1. `pce_items` + `data_validasi_ejm_expansion_joints`
2. `ejm_special_materials` (flange)
3. `cost_products`:
- `EJM_FLANGE_GRINDING_PAINTING`
- `EJM_MANPOWER_HOUR`

### Struktur hitung

1. Left side + right side (material + grinding/painting).
2. Ditambah manpower.

### Output utama

1. `total_price`

## 4.7 Detail EJM Final (`ejm_detail_ejms`)

### Sumber subtotal komponen

1. Bellows: `ejm_detail_bellows.total_price`
2. Collar: `ejm_detail_collars.total_price`
3. Metal Bellows: `ejm_detail_metal_bellows.grand_total`
4. Pipe End: `ejm_detail_pipe_ends.total_price`
5. Flange: `ejm_detail_flanges.total_price`

### Sumber proses final

1. `ejm_process_definitions` untuk:
- `component_type = 'EJM Production'` atau `Generic`
- `process_name = Assembly, Painting, Finishing`
2. `ejm_process_times.minutes_inner`
- prioritas per NB item
- fallback ke baris `NB = null`

### Sumber manpower final

1. `cost_products.EJM_MANPOWER_HOUR`

### Rumus final

1. `total_time_hour = (assembly + painting + finishing) / 60`
2. `manpower_cost = total_time_hour * manpower_rate_per_hour`
3. `total = bellows + collar + metal_bellows + pipe_end + flange + manpower_cost`
4. `margin_amount = total * (margin_percent / 100)`
5. `grand_total = total + margin_amount`

### Margin estimator

1. Input manual di UI Detail EJM.
2. Disimpan di `ejm_detail_ejms.margin_percent`.

## 5. Mapping Tampilan ke Sumber

## 5.1 List Order (`pce-order-list`)

1. Kolom `Total EJM` <- `ejm_detail_ejms.total`
2. Kolom `Margin (%)` <- `ejm_detail_ejms.margin_percent`
3. Kolom `Grand Total EJM` <- `ejm_detail_ejms.grand_total`

## 5.2 List PCE Header (`pce_headers`)

1. Kolom `Grand Total (IDR)` <- SUM `ejm_detail_ejms.grand_total` seluruh item dalam header.

## 6. Titik Revisi Cepat (tanpa ubah kode)

1. Revisi dimensi default NB/NOC -> `data_validasi_ejm_expansion_joints`
2. Revisi menit proses -> `ejm_process_times`
3. Revisi definisi proses -> `ejm_process_definitions`
4. Revisi harga material -> `ejm_special_materials`
5. Revisi rate biaya -> `cost_products`
6. Revisi margin tender -> UI `Detail EJM`

## 7. Kontrol Error (Strict DB-Only)

1. Jika proses/rate/detail sumber belum ada, generate dibatalkan.
2. Tujuan: mencegah angka asumsi tersembunyi.
3. Dampak: audit trail jelas, revisi bisa dilacak ke tabel sumber.

## 8. Checklist Presentasi (Operasional)

1. Cek master rate di `cost_products` lengkap.
2. Cek proses `Bellows/Collar/Pipe End/EJM Production` lengkap.
3. Generate semua detail komponen.
4. Generate Detail EJM final.
5. Isi margin estimator.
6. Validasi nilai di List Order dan List PCE Header.

## 9. Rumus Harga Material Per Detail

## 9.1 Detail Tube

1. Hitung geometri:
- `BL = NOC * Pitch`
- `OALB = (2*LC) + BL`
- `area_m2 = (OALB/1000) * (width_mm/1000) * ply`
- `volume_m3 = area_m2 * (thk_mm/1000)`
- `weight_kg = volume_m3 * 7850`
- `weight_gr = weight_kg * 1000`

2. Pemilihan basis harga material (prioritas):
- jika `price_gram` ada -> `harga_material = weight_gr * price_gram`
- else jika `price_kg` ada -> `harga_material = weight_kg * price_kg`
- else jika `price_sqm` ada -> `harga_material = area_m2 * price_sqm`

## 9.2 Detail Bellows

1. `width_inner = pi * ID`
2. `width_outer = pi * OD`
3. `square_inner = (width_inner * length_inner)/1,000,000`
4. `square_outer = (width_outer * length_outer)/1,000,000`
5. `cost_raw_material = ((square_inner + square_outer) * ply) * price_sqm * (1 + spare/100)`

## 9.3 Detail Collar

1. `width = pi * OD`
2. `square = (width * length)/1,000,000`
3. `qty_kanan_kiri = 2`
4. `cost_raw_material = (square * qty_kanan_kiri * price_sqm) * (1 + spare/100)`

## 9.4 Detail Metal Bellows

1. `square = (width * length)/1,000,000`
2. `harga_bellows = square * ply * price_sqm_bellow * (1 + spare/100)`
3. `harga_collar = square * 2 * price_sqm_collar * (1 + spare/100)`
4. Tambah consumable + labor rate:
- `welding_rod_qty * rate_welding_rod`
- `mesin_qty * rate_mesin`
- `manpower_qty * rate_manpower`
- `grinda_qty * rate_grinda`
- `disc_qty * rate_disc`
5. `total = semua komponen di atas`
6. `grand_total = total * qty_item`

## 9.5 Detail Pipe End

1. `width = pi * OD`
2. `square = (width * length)/1,000,000`
3. `cost_raw_material = square * price_sqm`

## 9.6 Detail Flange

1. `base_flange_price` diambil dari material:
- prioritas `price_sqm`, lalu `price_kg`, lalu `price_gram`
2. `left_total = (left_qty * base_flange_price) + (left_qty * rate_grinding_painting)`
3. `right_total = (right_qty * base_flange_price) + (right_qty * rate_grinding_painting)`

## 10. Penentuan Menit Proses, Jumlah Mesin, dan Manpower

## 10.1 Menit Proses dari mana

1. Semua menit proses utama diambil dari:
- `ejm_process_definitions` (nama proses dan component)
- `ejm_process_times` (nilai menit per NB/NOC)
2. Generator memilih proses berdasarkan `component_type`:
- Bellows -> `Cutting Shearing`, `Rolling`, `Seam Welding`, `Hydro Forming`
- Collar -> `Cutting Shearing`, `Rolling`, `Welding`
- Pipe End -> `Cutting`, `Bevel`, `Grinding`
- EJM Final -> `Assembly`, `Painting`, `Finishing`
3. Untuk EJM Final, jika per-NB tidak ada maka fallback ke baris `NB = null`.

## 10.2 Jumlah mesin/manpower saat ini di kode

1. Detail Bellows:
- `manpower_qty = 2`
- machine cost berbasis `total_time_minute * EJM_MACHINE_MINUTE`

2. Detail Collar:
- `quantity (manpower) = 2`
- machine cost berbasis `total_time_minute * EJM_MACHINE_MINUTE`

3. Detail Pipe End:
- `quantity (manpower) = 2`
- machine cost berbasis `total_time_minute * EJM_MACHINE_MINUTE`

4. Detail Metal Bellows:
- `welding_rod_qty = 1`
- `mesin_qty = 1`
- `manpower_qty = 2`
- `grinda_poles_qty = 1`
- `disc_poles_qty = 1`

5. Detail Flange:
- `left_qty = 1`
- `right_qty = 1`
- `manpower_qty = 2`

## 10.3 Rumus biaya proses/manpower

1. Jika rate per menit:
- `biaya = menit * rate_per_minute * qty`

2. Jika rate per jam:
- `biaya = (menit/60) * rate_per_hour * qty`

3. Khusus Metal Manpower:
- prioritas `EJM_METAL_MANPOWER`
- jika kosong -> fallback `EJM_MANPOWER_HOUR / 60`

## 11. Catatan Revisi Lanjutan (agar lebih fleksibel)

1. Qty manpower/mesin saat ini sebagian masih fixed di kode (1 atau 2).
2. Jika ingin full configurable, disarankan tambah master:
- `ejm_process_resource_defaults` (opsional) berisi qty default manpower/mesin/consumable per proses per komponen.
3. Dengan itu estimator tidak perlu ubah kode saat ada perubahan standar resource.
