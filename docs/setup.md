# Setup dan pemeriksaan AdventureAja

## Menjalankan di Laragon

1. Letakkan proyek di `C:\laragon\www\AdventureAja`.
2. Jalankan Laragon dan aktifkan Apache serta MySQL.
3. Import `database/db_adventureaja.sql` melalui phpMyAdmin. Database yang digunakan bernama `db_adventureaja`.
4. Periksa kredensial database pada `config/koneksi.php` jika konfigurasi MySQL lokal berbeda dari pengaturan bawaan Laragon.
5. Buka `http://localhost/AdventureAja/`.

## Akun awal

- Admin: `admin@adventureaja.com`
- Password awal: `admin123`

Akun contoh lain pada SQL dump memakai password awal teks biasa. Saat login pertama berhasil, password tersebut diperbarui menjadi hash. Akun baru dari halaman register langsung menyimpan password menggunakan `password_hash()`.

## Fitur database

- Register dan login menggunakan tabel `users`.
- CRUD kategori menggunakan tabel `categories`.
- CRUD produk menggunakan tabel `products`; gambar unggahan disimpan di `uploads/products/`.
- Checkout user dan pengelolaan transaksi admin menggunakan tabel `transactions` dan `transaction_details`.
- Widget dashboard admin menghitung jumlah kategori, produk, dan transaksi dari database.
- Laporan produk dapat dicetak atau disimpan sebagai PDF lewat dialog cetak browser.

Semua halaman admin memerlukan role admin. Dashboard user hanya dapat dibuka oleh akun dengan role user.

## Checklist pemeriksaan lokal

1. Login sebagai admin dan coba tambah, edit, serta hapus kategori dan produk.
2. Tambahkan produk dengan gambar dan pastikan gambar muncul di katalog user.
3. Periksa widget jumlah produk setelah menambah atau menghapus produk.
4. Buka laporan produk, pilih **Cetak / Simpan PDF**, lalu pilih **Save as PDF** pada dialog cetak.
5. Register akun user, login, buat pesanan, lalu pastikan pesanan tampil di menu Transaksi admin.
6. Ubah status transaksi sebagai admin dan pastikan perubahan tampil di dashboard user.
7. Logout, lalu coba buka halaman admin tanpa login. Server harus mengarahkan kembali ke halaman login.

Composer tidak digunakan untuk dependency proyek saat ini. Jika tugas meminta bukti pemasangan Composer, jalankan `composer --version` dan sertakan screenshot hasilnya pada dokumen pengumpulan.
