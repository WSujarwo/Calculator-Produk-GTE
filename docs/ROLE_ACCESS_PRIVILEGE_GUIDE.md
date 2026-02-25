# Panduan Privilege Role Access (Laravel + Spatie Permission)

Panduan ini untuk tim yang baru belajar Laravel agar paham alur dari membuat modul baru sampai privilege-nya muncul di halaman **Role Access**.

## 1. Konsep Dasar

Aplikasi ini memakai pola permission:
- `module.action`
- Contoh: `master.products.view`, `master.products.create`

Action standar yang dipakai:
- `view`
- `create`
- `edit`
- `delete`
- `import`
- `export`

Semua modul dan action dikonfigurasi di:
- `config/module_permissions.php`

Seeder akan membuat permission otomatis dari config tersebut:
- `database/seeders/ModulePermissionSeeder.php`

## 2. Menambah Modul Baru Agar Muncul di Role Access

Contoh: tambah modul **Material**.

### Langkah A - Daftarkan modul di config

Edit file `config/module_permissions.php`, lalu tambah modul di group yang sesuai:

```php
'groups' => [
    'Master Data' => [
        'master.products' => 'Master Products',
        'master.shapes' => 'Master Shapes',
        'master.product-shapes' => 'Product-Shape Mapping',
        'master.type-configs' => 'Type / Configuration',
        'master.materials' => 'Master Materials', // baru
    ],
],
```

Dengan satu baris itu, sistem akan otomatis mengenali permission:
- `master.materials.view`
- `master.materials.create`
- `master.materials.edit`
- `master.materials.delete`

### Langkah B - Generate permission ke database

Jalankan:

```bash
php artisan db:seed --class=ModulePermissionSeeder
php artisan optimize:clear
```

`db:seed` membuat record permission.
`optimize:clear` membersihkan cache agar perubahan langsung terbaca.

## 3. Lindungi Controller Dengan Permission Middleware

Di controller modul (contoh `MaterialController`), pasang middleware:

```php
public function __construct()
{
    $this->middleware('permission:master.materials.view')->only(['index']);
    $this->middleware('permission:master.materials.create')->only(['create', 'store']);
    $this->middleware('permission:master.materials.edit')->only(['edit', 'update']);
    $this->middleware('permission:master.materials.delete')->only(['destroy']);
}
```

Tujuan:
- Security di backend tetap aman, walaupun user mencoba akses URL langsung.

## 4. Lindungi Tampilan (Blade) Dengan `@can`

Di file blade index/list modul, sembunyikan tombol berdasarkan permission.

### Tombol Create

```blade
@can('master.materials.create')
    <a href="{{ route('master.materials.create') }}">Create</a>
@endcan
```

### Tombol Edit

```blade
@can('master.materials.edit')
    <a href="{{ route('master.materials.edit', $item) }}">Edit</a>
@endcan
```

### Tombol Delete

```blade
@can('master.materials.delete')
    <form method="POST" action="{{ route('master.materials.destroy', $item) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Delete</button>
    </form>
@endcan
```

### Jika butuh cek lebih dari satu permission

```blade
@canany(['master.materials.edit', 'master.materials.delete'])
    <th>Action</th>
@endcanany
```

## 5. Route yang Perlu Ada

Contoh route resource modul material:

```php
Route::prefix('master')->name('master.')->middleware('auth')->group(function () {
    Route::resource('materials', MaterialController::class)->except(['show']);
});
```

Untuk halaman Role Access, route sudah tersedia:
- `GET /setting`
- `PUT /setting/roles/{role}`
- `PUT /setting/users/{user}`

## 6. Cara Pakai Halaman Role Access

Halaman ada di menu sidebar: **Role Access** (admin only).

Alur pakai:
1. Pilih role (dropdown).
2. Cari modul (search) jika modul banyak.
3. Checklist privilege per action.
4. Klik **Simpan Permission**.

Tips:
- Ada checkbox massal per kolom (`View/Create/Edit/Delete`) untuk centang semua cepat.
- Modul dibagi per group (collapse) agar tidak terlalu panjang.

## 7. Alur Kerja Tim (Supaya Aman Pull/Push Antar Laptop)

Urutan standar setelah pull terbaru:

```bash
composer install
php artisan migrate --seed
php artisan optimize:clear
```

Jika hanya update permission tanpa migration baru:

```bash
php artisan db:seed --class=ModulePermissionSeeder
php artisan optimize:clear
```

## 8. Troubleshooting

### Permission tidak muncul di halaman Role Access
- Pastikan modul sudah ditambah di `config/module_permissions.php`.
- Jalankan `php artisan db:seed --class=ModulePermissionSeeder`.
- Jalankan `php artisan optimize:clear`.

### Tombol sudah disembunyikan tapi URL masih bisa diakses
- Berarti middleware permission di controller belum dipasang.
- Wajib pasang middleware pada method controller.

### Muncul error middleware / permission
- Pastikan user punya role.
- Pastikan role punya permission yang sesuai.
- Jalankan ulang seeder + clear cache.

## 9. Checklist Cepat Saat Build Modul Baru

- Tambah modul di `config/module_permissions.php`.
- Jalankan seeder permission.
- Pasang middleware permission di controller.
- Pasang `@can` / `@canany` di blade.
- Test login tiap role (admin/non-admin).
- Commit file code + config + seeder terkait.
