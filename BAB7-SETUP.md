# Implementasi BAB 7 — AdventureAja

## Menjalankan di Laragon
1. Salin/clone repository ke `C:\laragon\www\AdventureAja`.
2. Jalankan Laragon dan klik **Start All**.
3. Buka phpMyAdmin melalui Laragon.
4. Import file `db_adventureaja.sql`. Database yang digunakan bernama `db_adventureaja`.
5. Periksa `config/koneksi.php`. Default Laragon biasanya memakai host `localhost`, user `root`, password kosong. Sesuaikan jika konfigurasi MySQL lokal berbeda.
6. Buka `http://localhost/AdventureAja/`.

## Akun awal dari SQL dump
- Admin: `admin@adventureaja.com`
- Password awal: `admin123`

Akun contoh lain di SQL dump juga masih memiliki password awal teks biasa. Saat login pertama kali berhasil, sistem memperbarui password akun tersebut menjadi hash. Akun yang dibuat melalui register langsung menyimpan password dengan `password_hash()`.

## File baru BAB 7
- `config/koneksi.php`: koneksi MySQL.
- `auth.php`: session, pemeriksaan role, escaping output, dan token CSRF.
- `categories-process.php`: proses Create, Update, Delete kategori dengan prepared statement.

## File yang diperbarui
- `register.php` dan `login.php`: menggunakan tabel `users`.
- `admin.php`, `categories.php`, `categories-entry.php`, `transaction.php`, dan `transaction-entry.php`: dilindungi role admin.
- `user-dashboard.php`: khusus role user.
- `categories.php` dan `categories-entry.php`: CRUD kategori menggunakan tabel `categories` di MySQL, bukan localStorage.

## Pengujian
1. Login admin dengan akun awal, lalu buka dashboard admin.
2. Buka `categories.php`, tambah kategori, edit kategori, lalu hapus kategori yang tidak dipakai produk.
3. Register akun baru, login sebagai user.
4. Coba buka `admin.php` atau `categories.php` dari akun user. Server harus menolak akses.
5. Logout dan coba buka halaman admin langsung. Server harus mengarahkan ke login.

Catatan: CRUD yang diimplementasikan pada tahap ini adalah CRUD **kategori** sesuai salah satu data yang dikelola di dashboard admin. CRUD transaksi belum dipindahkan dari localStorage pada tahap ini.
