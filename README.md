# AdventureAja
AdventureAja: website penyewaan perlengkapan camping (tenda, sleeping bag, carrier, dll). Proyek Praktikum Pemrograman Web Informatika ITN Malang 2026
Link Figma: https://www.figma.com/design/druUAnHtPfXQvhPoKZJ163/AdventureAja?node-id=15-36&t=ais4KpuU87kRVffU-1

## Struktur proyek

- `assets/`: gambar yang dipakai halaman.
- `config/`: koneksi database.
- `css/` dan `js/`: stylesheet dan JavaScript.
- `data/`: data JSON lama.
- `database/`: SQL untuk membuat dan mengisi database.
- `docs/`: panduan setup dan pemeriksaan.
- `includes/`: autentikasi, session, dan bootstrap path aplikasi.
- `pages/admin/`, `pages/user/`, `pages/public/`: sumber halaman, dipisah berdasarkan area aplikasi.
- `actions/`: proses form dan endpoint perubahan data.
- `uploads/products/`: gambar produk yang diunggah admin.
- File PHP di root: route tipis untuk menjaga URL halaman dan endpoint lama tetap berlaku.

Panduan menjalankan proyek: [docs/setup.md](docs/setup.md).

## Tugas praktikum: CRUD data kedua

CRUD produk merupakan data yang dikerjakan pada tugas sebelumnya. Sebagai CRUD data kedua, dashboard admin mengelola transaksi penyewaan yang tersimpan di database MySQL:

- **Create:** pesanan dari checkout user tersimpan otomatis; admin juga dapat menambahkan transaksi secara manual.
- **Read:** admin melihat daftar transaksi, pelanggan, perlengkapan, tanggal sewa, total biaya, dan status.
- **Update:** admin mengubah status transaksi (menunggu konfirmasi, sedang disewa, selesai, atau dibatalkan).
- **Delete:** admin dapat menghapus transaksi.

Dashboard admin juga menampilkan widget jumlah kategori, produk, dan transaksi, termasuk transaksi yang sedang diproses. Laporan produk dapat dicetak atau disimpan sebagai PDF melalui dialog cetak browser.

Composer terpasang dan terdeteksi melalui `composer --version` (Composer 2.10.2, PHP 8.5.5). Output perintah ini dapat disertakan sebagai bukti instalasi.
