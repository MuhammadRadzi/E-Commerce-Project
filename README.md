# PROYEK E-COMMERCE - TOKO KOMPUTER

Website e-commerce berbasis PHP untuk toko perangkat keras komputer yang dilengkapi dengan keranjang belanja, dashboard admin, dan manajemen inventaris.

## INFORMASI PROYEK

- **Tipe**: Full-stack Web Application
- **Bahasa**: PHP 8.2.12, MySQL, JavaScript, HTML5, CSS3
- **Arsitektur**: Struktur menyerupai MVC dengan pemisahan fungsi (separation of concerns)

## FITUR

### Fitur Pelanggan
- Katalog produk dengan paginasi dan pencarian
- Tampilan stok secara real-time
- Sistem keranjang belanja
- Proses checkout yang aman
- Sistem autentikasi pengguna
- Desain responsif (mobile, tablet, desktop)

### Fitur Admin
- Dashboard admin dengan ringkasan inventaris
- Operasi CRUD untuk produk
- Unggah gambar dengan validasi
- Manajemen stok
- Kontrol akses berbasis peran pengguna (RBAC)
- Prepared statements untuk pencegahan SQL injection

### Keamanan
- Hashing kata sandi dengan bcrypt
- Pencegahan SQL injection (_prepared statements_)
- Perlindungan XSS (`htmlspecialchars`)
- Sanitasi input
- Manajemen sesi
- Siap untuk perlindungan CSRF

## TEKNOLOGI YANG DIGUNAKAN

### Backend
- PHP 8.2.12
- MySQL/MariaDB
- Autentikasi berbasis sesi

### Frontend
- Markup semantik HTML5
- CSS3 dengan CSS Grid dan Flexbox
- Vanilla JavaScript (tanpa framework)
- State pemuatan (loading states) dan animasi
- Skeleton screens

### Development Tools
- XAMPP (Apache + MySQL)
- Git untuk kontrol versi

## STRUKTUR PROYEK

```
project-ecommerce/
├── config/
│   └── database.php          # Koneksi database & fungsi pembantu
├── includes/
│   ├── functions.php         # Fungsi logika bisnis
│   └── navigation.php        # Komponen navigasi
├── assets/
│   ├── css/
│   │   └── style.css         # Stylesheet utama
│   ├── js/                   # File JavaScript
│   ├── img/
│   │   ├── products/         # Gambar produk
│   │   └── ui/               # Aset UI
│   └── fonts/                # Font web
├── admin/
│   ├── index.php             # Dashboard Admin
│   └── products/
│       ├── add.php           # Add produk
│       ├── edit.php          # Edit produk
│       └── delete.php        # Delete produk
├── auth/
│   ├── login_process.php     # Handler Login
│   └── logout.php            # Handler Logout
├── process/
│   ├── product_add.php       # Proses tambah produk
│   └── product_edit.php      # Proses edit produk
├── public/
│   └── checkout.php          # Proses checkout
├── classes/                  # kelas OOP(penggunaan masa depan)
├── index.php                 # Halaman katalog utama
├── beli.php                  # Halaman tambah ke keranjang
├── keranjang.php             # Halaman keranjang belanja
├── login.php                 # Halaman login
├── .htaccess                 # Konfigurasi Apache
└── .gitignore                # Aturan Git ignore
```

## SKEMA DATABASE

### Tabel: barang (produk)
```sql
CREATE TABLE barang (
    id_barang INT PRIMARY KEY AUTO_INCREMENT,
    nama_barang VARCHAR(255) NOT NULL,
    jenis_barang VARCHAR(100),
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(10,2) NOT NULL,
    kondisi ENUM('Baru', 'Bekas', 'Rusak'),
    lokasi_rak VARCHAR(10),
    gambar VARCHAR(255) DEFAULT 'no-image.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel: users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## PENGGUNAAN

### Alur Pelanggan
1. Telusuri produk di `index.php`
2. Login (diperlukan untuk melakukan pembelian)
3. Klik "Beli Sekarang" pada produk
4. Pilih jumlah dan tambahkan ke keranjang
5. Lihat keranjang dan lanjut ke checkout
6. Stok akan berkurang setelah checkout berhasil

### Alur Admin
1. Login dengan kredensial admin
2. Akses dashboard admin
3. Kelola produk (Tambah/Edit/Hapus)
4. Unggah gambar produk
5. Pantau tingkat inventaris

## LOGIKA BISNIS UTAMA

### Stock Management
- Stok TIDAK berkurang saat menambahkan ke keranjang
- Stok HANYA berkurang saat proses checkout
- Berbasis transaksi dengan pembatalan (rollback) jika gagal
- Penguncian `FOR UPDATE` mencegah terjadinya race conditions

### Keranjang Belanja
- Penyimpanan berbasis sesi
- Mendukung akumulasi jumlah barang
- Validasi terhadap stok yang tersedia
- Dikosongkan setelah checkout berhasil

### Langkah Keamanan
- Prepared statements untuk semua kueri database
- Hashing kata sandi dengan `password_verify()`
- Sanitasi input melalui helper `input()`
- Autentikasi berbasis sesi
- Kontrol akses berbasis peran (RBAC)

## API / FUNGSI

### Fungsi Helper (config/database.php)
```php
formatRupiah($angka): Memformat mata uang
tampilkanPesan($jenis, $pesan): Menampilkan pesan
input($data): Sanitasi input
breadcrumb($judul): Menghasilkan breadcrumb
```

### Logika Bisnis (includes/functions.php)
```php
getBarang($start, $limit, $keyword): Mengambil data produk
prosesPembelian($id, $qty): Memproses pembelian
checkSystemHealth(): Pemeriksaan kesehatan sistem
```

## PANDUAN PENGEMBANGAN

### Gaya Kode
- Gunakan prepared statements untuk kueri database
- Sanitasi semua input pengguna
- Ikuti standar pengkodean PHP PSR-12
- Gunakan elemen HTML5 semantik
- Jaga agar CSS tetap terorganisir dengan komentar yang jelas

### Penamaan File
- File PHP: huruf kecil dengan garis bawah (_underscore_)
- Kelas: PascalCase
- Fungsi: camelCase
- Tabel database: huruf kecil tunggal (_singular_)

### Daftar Periksa Keamanan
- [ ] Semua kueri database menggunakan prepared statements
- [ ] Input pengguna disanitasi dengan helper `input()`
- [ ] Kata sandi di-hash dengan `password_hash()`
- [ ] Variabel sesi divalidasi
- [ ] Validasi unggah file (tipe, ukuran)
- [ ] Pesan kesalahan tidak mengekspos info sistem

## PENGUJIAN

### Daftar Periksa Pengujian Manual
- [ ] Fungsi Login/Logout
- [ ] Pencarian produk dan paginasi
- [ ] Tambah ke keranjang (satu dan banyak item)
- [ ] Hapus dari keranjang
- [ ] Proses checkout
- [ ] Validasi stok
- [ ] Operasi CRUD Admin
- [ ] Unggah gambar
- [ ] Kontrol akses (user vs admin)
- [ ] Penanganan checkout bersamaan (_concurrent_)

### Skenario Uji
1. Tambah item ke keranjang, hapus, tambah lagi
2. Tambah jumlah melebihi stok
3. Checkout dengan stok tidak mencukupi
4. Dua pengguna checkout secara bersamaan
5. Unggah tipe file yang tidak valid
6. Upaya SQL injection
7. Upaya XSS dalam formulir

## OPTIMASI PERFORMA

### Database
- Tambahkan indeks pada kolom yang sering dikueri
- Gunakan LIMIT untuk paginasi
- Implementasikan _query caching_

### Frontend
- _Lazy load_ gambar produk
- Minifikasi CSS dan JavaScript
- Aktifkan caching browser melalui `.htaccess`

### Backend
- Gunakan opcode caching (OPcache)
- Pembersihan sesi untuk sesi lama
- _Database connection pooling_

## LISENSI

Proyek ini bertujuan untuk pendidikan.
