```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdventureAja | Form Transaksi</title>

    <link rel="icon" href="assets/AdventureAja.png">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css"
          rel="stylesheet">
</head>
<body>

   <div class="dashboard">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="admin.php" class="logo">
                    Adventure<span>Aja</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="admin.php" class="nav-link">
                    <i class="bx bxs-dashboard"></i>
                    <span>Dashboard</span>
                </a>

                <a href="categories.php" class="nav-link">
                    <i class="bx bxs-category"></i>
                    <span>Kategori</span>
                </a>

                <a href="transaction.php" class="nav-link active">
                    <i class="bx bxs-receipt"></i>
                    <span>Transaksi</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <span>AdventureAja Admin</span>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="topbar">
                <button
                    type="button"
                    class="sidebar-toggle"
                    id="sidebarToggle"
                    aria-label="Buka atau tutup sidebar"
                    aria-controls="sidebar"
                    aria-expanded="true">
                    ☰
                </button>

                <div class="topbar-info">
                    <span class="topbar-label">ADMIN PANEL</span>
                    <h2>Form Transaksi</h2>
                </div>
            </header>

            <!-- JUDUL HALAMAN -->
            <section class="page-heading">
                <div>
                    <p class="eyebrow">MANAJEMEN PENYEWAAN</p>
                    <h1 id="transactionPageTitle">Tambah Transaksi</h1>
                    <p class="page-description">
                        Masukkan informasi penyewa dan detail penyewaan
                        perlengkapan camping.
                    </p>
                </div>
            </section>

            <!-- FORM TRANSAKSI -->
            <section class="form-card">
                <div class="form-card-header">
                    <div>
                        <h2>Informasi Transaksi</h2>
                        <p>Lengkapi seluruh data yang diperlukan.</p>
                    </div>

                    <i class="bx bxs-receipt form-header-icon"></i>
                </div>

                <form id="transactionForm">
                    <input type="hidden" id="transactionId" name="transactionId">

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="kodeTransaksi">
                                Kode Transaksi
                            </label>
                            <input
                                type="text"
                                id="kodeTransaksi"
                                name="kodeTransaksi"
                                placeholder="Contoh: TRX-001"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="namaPenyewa">
                                Nama Penyewa
                            </label>
                            <input
                                type="text"
                                id="namaPenyewa"
                                name="namaPenyewa"
                                placeholder="Masukkan nama penyewa"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="nomorTelepon">
                                Nomor Telepon
                            </label>
                            <input
                                type="tel"
                                id="nomorTelepon"
                                name="nomorTelepon"
                                placeholder="Contoh: 08xxxxxxxxxx"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="barang">
                                Perlengkapan Camping
                            </label>
                            <select id="barang" name="barang" required>
                                <option value="">Pilih perlengkapan</option>
                                <option value="Tenda 3 Layer">
                                    Tenda 3 Layer
                                </option>
                                <option value="Carrier 60L">
                                    Carrier 60L
                                </option>
                                <option value="Sleeping Bag">
                                    Sleeping Bag
                                </option>
                                <option value="Lampu Tenda">
                                    Lampu Tenda
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">
                                Jumlah Barang
                            </label>
                            <input
                                type="number"
                                id="jumlah"
                                name="jumlah"
                                min="1"
                                value="1"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="totalBiaya">
                                Total Biaya (Rp)
                            </label>
                            <input
                                type="number"
                                id="totalBiaya"
                                name="totalBiaya"
                                min="0"
                                placeholder="Masukkan total biaya"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="tanggalSewa">
                                Tanggal Mulai Sewa
                            </label>
                            <input
                                type="date"
                                id="tanggalSewa"
                                name="tanggalSewa"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="tanggalKembali">
                                Tanggal Pengembalian
                            </label>
                            <input
                                type="date"
                                id="tanggalKembali"
                                name="tanggalKembali"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="status">
                                Status Transaksi
                            </label>
                            <select id="status" name="status" required>
                                <option value="">Pilih status</option>
                                <option value="Menunggu">Menunggu</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Sedang Disewa">
                                    Sedang Disewa
                                </option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">
                                    Dibatalkan
                                </option>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="alamat">
                                Alamat Penyewa
                            </label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="4"
                                placeholder="Masukkan alamat penyewa"
                                required></textarea>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="catatan">
                                Catatan (Opsional)
                            </label>
                            <textarea
                                id="catatan"
                                name="catatan"
                                rows="3"
                                placeholder="Tambahkan catatan jika diperlukan"
                            ></textarea>
                        </div>

                    </div>

                    <!-- TOMBOL AKSI -->
                    <div class="form-actions">
                        <a href="transaction.php" class="btn-secondary">
                            <i class="bx bx-arrow-back"></i>
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="primary-button form-submit"
                            id="transactionSubmitButton">
                            Simpan Transaksi
                        </button>
                    </div>

                </form>
            </section>

            <footer class="admin-footer">
                <p>© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p>
            </footer>

        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
```