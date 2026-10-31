
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AdventureAja | Transaksi</title>

    <!-- Menghubungkan CSS Admin -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="dashboard">

        <!-- Sidebar navigasi Admin -->
        <aside class="sidebar" id="sidebar">

            <div class="sidebar-logo">
                <h2>Adventure<span>Aja</span></h2>
                <p>ADMIN PANEL</p>
            </div>

            <nav class="sidebar-nav">
                <p class="menu-label">MENU UTAMA</p>

                <a href="admin.php" class="nav-link">
                    <span class="nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="categories.php" class="nav-link">
                    <span class="nav-icon">▤</span>
                    <span>Kategori</span>
                </a>

                <a href="transaction.php" class="nav-link active">
                    <span class="nav-icon">▣</span>
                    <span>Transaksi</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="admin-avatar">A</div>

                <div class="admin-info">
                    <h4>Administrator</h4>
                    <p>Admin AdventureAja</p>
                </div>
            </div>

        </aside>

        <!-- Konten utama -->
        <main class="main-content">

            <header class="topbar">

                <div class="topbar-left">
                    <button
                        type="button"
                        class="sidebar-toggle"
                        id="sidebarToggle"
                        aria-label="Buka atau tutup sidebar"
                        aria-controls="sidebar"
                        aria-expanded="true"
                    >
                        ☰
                    </button>

                    <div class="page-heading">
                        <p class="breadcrumb">Halaman / Transaksi</p>
                        <h1>Manajemen Transaksi</h1>
                    </div>
                </div>

                <div class="topbar-info">
                    <div class="status-indicator"></div>
                    <span>Admin Panel</span>
                </div>

            </header>

            <!-- Pengantar halaman -->
            <section class="welcome-section transaction-welcome">
                <div class="welcome-text">
                    <p class="eyebrow">MANAJEMEN PENYEWAAN</p>

                    <h2>Pantau Transaksi Penyewaan</h2>

                    <p>
                        Pantau pesanan pelanggan dan kelola status
                        penyewaan perlengkapan camping AdventureAja.
                    </p>
                </div>
            </section>

            <!-- Ringkasan transaksi -->
            <section class="transaction-summary">

                <!-- Total Transaksi -->
                <h2 id="totalTransactions">0</h2>

                <!-- Menunggu Konfirmasi -->
                <h2 id="pendingTransactions">0</h2>

                <!-- Sedang Disewa -->
                <h2 id="activeTransactions">0</h2>

                <!-- Selesai -->
                <h2 id="completedTransactions">0</h2>

            </section>

            <!-- Daftar transaksi -->
            <section class="transaction-management">

                <div class="section-heading">
                    <div>
                        <h2>Daftar Transaksi</h2>
                        <p>Informasi penyewaan pelanggan.</p>
                    </div>

                    <a
                        href="transaction-entry.php"
                        class="primary-button add-button"
                    >
                        <span>+</span>
                        Tambah Transaksi
                    </a>
                </div>

                <!-- Filter visual, belum aktif -->
                <div class="transaction-toolbar">
                    <div class="search-placeholder">
                        <span>⌕</span>
                        <span>Cari nama pelanggan atau barang...</span>
                    </div>

                    <div class="filter-placeholder">
                        Semua Status
                        <span>⌄</span>
                    </div>
                </div>

                <div class="table-container">
                    <table class="transaction-table">

                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Pelanggan</th>
                                <th>Perlengkapan</th>
                                <th>Tanggal Sewa</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        
                        <tbody id="transactionTableBody">
                            <!-- Data transaksi ditampilkan oleh JavaScript -->

                        </tbody>

                    </table>
                </div>

                <div class="table-footer">
                    <p id="transactionCount">Memuat data transaksi...</p>
                    <span class="table-page-number">Halaman 1</span>
                </div>

            </section>

            <footer class="dashboard-footer">
                <p>
                    © 2026 AdventureAja.
                    Temani setiap langkah petualanganmu.
                </p>
            </footer>

        </main>
    </div>

    <!-- JavaScript untuk interaksi sidebar -->
    <script src="js/script.js"></script>

</body>
</html>
