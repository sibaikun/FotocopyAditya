<h1 align="center">🖨️ Fotocopy Aditya</h1>

<p align="center">
  Sistem manajemen toko fotokopi dan ATK: kasir, stok barang, jasa, nota, dan laporan keuangan dalam satu aplikasi web.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?logo=alpinedotjs&logoColor=white" alt="Alpine.js">
</p>

---

## 📌 Tentang

**Fotocopy Aditya** adalah aplikasi web internal untuk membantu operasional toko
**Foto Copy Aditya** di Jl. Kramatsari 1 No. 12A, Kel. Kramatsari, Kec. Pekalongan Barat, Kota Pekalongan.

Toko ini melayani fotokopi, print, penjilidan, laminating, penjualan ATK dan materai, serta loket pembayaran.
Aplikasi ini menggantikan pencatatan manual dengan sistem yang menghitung stok, pendapatan, dan pengeluaran secara otomatis.

> Seluruh halaman pengelolaan **wajib login** terlebih dahulu. Pendaftaran akun publik sengaja dimatikan.

## ✨ Fitur

### 🛒 Kasir dan Nota
- Jual **barang** dan **jasa** dalam satu nota.
- Barang bisa dijual **eceran** (per item) atau **borongan** (per pak/kardus) dengan harga berbeda.
- Harga jasa bisa diubah langsung saat transaksi (misalnya biaya admin loket).
- Kembalian dihitung otomatis, tombol **Uang pas** tersedia.
- Stok berkurang otomatis, dan stok tidak cukup akan ditolak.
- Nota siap cetak dengan nomor otomatis per hari (`INV-20261114-001`).
- Riwayat nota lengkap.

### 📦 Manajemen Barang
- Data barang dengan **gambar opsional** (otomatis diperkecil sebelum diunggah, bisa diklik untuk diperbesar).
- Satuan dropdown (pcs, lembar, rim, dan lainnya) dan **satuan kemasan** (kardus/pak/box beserta isinya).
- Harga eceran, harga borongan, dan **harga beli** per satuan apa pun, plus nama toko/distributor.
- Pencarian **langsung saat mengetik**, serta label stok **Habis / Menipis / Aman**.
- Tombol **+ Stok** di tiap baris untuk menambah stok dengan cepat.

### ⬇️ Barang Masuk
- Pilih barang lewat **popup yang bisa dicari**.
- Catat dalam satuan besar (misalnya 2 kardus), sistem **mengonversi otomatis** ke item (2 × 12 = 24 pcs).
- Total pengeluaran dihitung otomatis dari harga beli barang.

### ⬆️ Barang Keluar
- Penjualan dari Kasir **tercatat otomatis** lengkap dengan link ke notanya.
- Catat manual untuk barang **rusak, hilang, atau dipakai sendiri**.
- Filter berdasarkan alasan.

### 🏷️ Jasa
- Tambah, ubah, dan hapus layanan (fotokopi, print, laminating, dan lainnya).
- **Toggle switch** untuk mengaktifkan atau menonaktifkan. Jasa nonaktif tidak muncul di Kasir.

### 📊 Dashboard dan Laporan
- Dashboard: omzet hari ini, jumlah nota, omzet bulan ini, transaksi terakhir, stok menipis.
- Laporan **harian, mingguan, dan bulanan**: pendapatan, pengeluaran, selisih, rincian per hari, 5 item terlaris, dan pembagian pendapatan dari barang vs jasa.

### 🎨 Tampilan
- Sidebar navy, responsif untuk desktop dan HP.
- Transisi halaman yang halus (sidebar diam, isi halaman bergeser).
- Halaman login dengan foto latar toko.

## 🧰 Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Autentikasi | Laravel Breeze (Blade) |
| Database | MySQL / MariaDB |
| Tampilan | Blade, Tailwind CSS |
| Interaksi | Alpine.js |
| Build asset | Vite |
| Lingkungan lokal | Laragon (Windows) |

## 🖼️ Tampilan

| Dashboard | Kasir |
|---|---|
| ![Dashboard](docs/screenshots/dashboard.png) | ![Kasir](docs/screenshots/kasir.png) |

| Data Barang | Laporan |
|---|---|
| ![Barang](docs/screenshots/barang.png) | ![Laporan](docs/screenshots/laporan.png) |

## 🚀 Instalasi

### Kebutuhan
- PHP **8.3** atau lebih baru
- Composer
- Node.js dan npm
- MySQL / MariaDB

### Langkah

```bash
# 1. Unduh project
git clone <url-repository> fotocopy-aditya
cd fotocopy-aditya

# 2. Pasang dependensi
composer install
npm install

# 3. Salin file konfigurasi, lalu buat kunci aplikasi
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Buat database kosong bernama `fotocopyaditya`, lalu atur koneksinya di file `.env` (lihat bagian *Konfigurasi*).

```bash
# 4. Buat tabel dan akun pemilik
php artisan migrate --seed

# 5. Hubungkan folder penyimpanan (agar gambar barang tampil)
php artisan storage:link

# 6. Bangun CSS dan JavaScript
npm run build
```

### Menjalankan

**Dengan Laragon:** letakkan project di `C:\laragon\www\fotocopy-aditya`, klik **Start All**,
lalu buka `http://fotocopy-aditya.test`.

**Tanpa Laragon:**

```bash
php artisan serve
```

lalu buka `http://127.0.0.1:8000`.

### Akun pertama

Akun pemilik dibuat oleh seeder di `database/seeders/UserSeeder.php`. Lihat email dan password
di file itu, lalu **segera ganti password** setelah login pertama lewat menu **Pengaturan**.

## ⚙️ Konfigurasi

Contoh isi `.env`:

```env
APP_NAME="Fotocopy Aditya"
APP_URL=http://fotocopy-aditya.test
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fotocopyaditya
DB_USERNAME=root
DB_PASSWORD=
```

Zona waktu `Asia/Jakarta` penting supaya transaksi dini hari tercatat di tanggal yang benar.

**Foto latar halaman login:** simpan sebagai `public/images/storelogin.png`.

## 📖 Alur Penggunaan

1. **Daftarkan barang** di menu **Barang**: nama, satuan, kemasan, harga, dan harga beli.
2. **Isi stok** lewat **Barang Masuk** (atau tombol **+ Stok** di daftar barang).
3. **Atur jasa** di menu **Jasa**.
4. **Jual** lewat **Kasir**, lalu cetak nota.
5. Pantau **Barang Keluar**, **Nota**, dan **Laporan** kapan saja.

### Konsep stok

- Stok **selalu disimpan dalam satuan terkecil** (misalnya pcs), jadi 1 kardus isi 12 sama dengan 12 pcs.
- Stok **tidak diubah langsung**. Setiap perubahan lewat riwayat pergerakan (`stock_movements`), sehingga selalu bisa dilacak.
- Nama, harga, dan satuan **disalin ke nota dan riwayat** saat transaksi, jadi mengubah harga hari ini tidak mengubah laporan lama.

## 🗄️ Struktur Database

| Tabel | Isi |
|---|---|
| `users` | Akun login (nama, email, password, role) |
| `products` | Data barang: satuan, kemasan, harga eceran/borongan/beli, gambar, toko, stok |
| `stock_movements` | Riwayat stok masuk dan keluar (alasan, nilai pembelian, tautan ke nota) |
| `services` | Daftar jasa, harga, dan status aktif |
| `transactions` | Nota: nomor, tanggal, total, dibayar, kasir |
| `transaction_items` | Isi nota: barang atau jasa, jumlah, satuan, harga, subtotal |

## 📁 Struktur Penting

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ProductController.php        # Barang
│   ├── StockInController.php        # Barang Masuk
│   ├── StockOutController.php       # Barang Keluar
│   ├── ServiceController.php        # Jasa
│   ├── TransactionController.php    # Kasir dan Nota
│   └── ReportController.php         # Laporan
└── Models/                          # Product, StockMovement, Service, Transaction, TransactionItem
resources/views/
├── layouts/app.blade.php            # Sidebar dan kerangka halaman
├── auth/login.blade.php             # Halaman login
├── products/  stock-in/  stock-out/
├── services/  transactions/  reports/
public/images/storelogin.png         # Foto latar login
```

## 🔧 Pemecahan Masalah

| Masalah | Solusi |
|---|---|
| Tampilan berantakan atau tanpa gaya | Jalankan `npm run build` (atau biarkan `npm run dev` menyala), hapus `public/hot` jika ada, lalu **Ctrl + F5** |
| Gambar barang tidak muncul | Jalankan `php artisan storage:link` |
| `fotocopy-aditya.test` tidak bisa dibuka | Pastikan Laragon **Start All**, **Auto virtual hosts** aktif, dan Laragon dijalankan sebagai Administrator |
| Error "Column not found" setelah memperbarui kode | Jalankan `php artisan migrate`. Untuk data percobaan: `php artisan migrate:fresh --seed` ⚠️ **menghapus seluruh data** |

> ⚠️ Jangan pernah memakai `migrate:fresh` di data toko yang sudah asli. Cadangkan database lebih dulu.

## 🗺️ Rencana Pengembangan

- [x] Login, kasir, nota, dan riwayat
- [x] Manajemen barang, jasa, barang masuk, dan barang keluar
- [x] Laporan harian, mingguan, dan bulanan
- [ ] Halaman profil toko untuk publik (layanan, alamat, peta, jam buka)
- [ ] Pembatalan nota (stok otomatis kembali)
- [ ] Hak akses berbeda untuk pemilik dan kasir
- [ ] Laba sesungguhnya (berdasarkan modal barang yang terjual)
- [ ] Koreksi stok / stok opname

## 📄 Lisensi

Proyek ini dibuat khusus untuk Foto Copy Aditya. Dibangun di atas [Laravel](https://laravel.com),
yang dirilis dengan [lisensi MIT](https://opensource.org/licenses/MIT).