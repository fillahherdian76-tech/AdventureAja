
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AdventureAja | Kategori</title>

    <!-- CSS khusus halaman admin -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="dashboard">

        <!-- Sidebar navigasi admin -->
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

                <a href="categories.php" class="nav-link active">
                    <span class="nav-icon">▤</span>
                    <span>Kategori</span>
                </a>

                <a href="transaction.php" class="nav-link">
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

        <!-- Konten halaman kategori -->
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
                        <p class="breadcrumb">Halaman / Kategori</p>
                        <h1>Kategori Perlengkapan</h1>
                    </div>
                </div>

                <div class="topbar-info">
                    <div class="status-indicator"></div>
                    <span>Admin Panel</span>
                </div>

            </header>

            <!-- Informasi halaman -->
            <section class="welcome-section category-welcome">
                <div class="welcome-text">
                    <p class="eyebrow">MANAJEMEN PERLENGKAPAN</p>

                    <h2>Kelola Kategori Camping</h2>

                    <p>
                        Atur data kategori perlengkapan agar inventaris
                        AdventureAja lebih terorganisasi.
                    </p>
                </div>
            </section>

            <!-- Area pengelolaan data -->
            <section class="category-section">

                <div class="section-heading">
                    <div>
                        <h2>Data Kategori</h2>
                        <p>Daftar kategori perlengkapan camping.</p>
                    </div>

                    <a href="categories-entry.php" class="primary-button add-button">
                        <span>+</span>
                        Tambah Kategori
                    </a>
                </div>

                <!-- Tabel kategori -->
                <div class="table-container">
                    <table class="transaction-table category-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                           <tbody id="categoryTableBody">
                                <!-- Baris kategori akan dibuat oleh JavaScript -->

                        </tbody>

                    </table>
                </div>

                <div class="table-footer">
                    <p id="categoryCount">Memuat data kategori...</p>
                    <span class="table-page-number">Halaman 1</span>
                </div>

            </section>

            <footer class="dashboard-footer">
                <p>© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p>
            </footer>

        </main>
    </div>

    <!-- Script untuk interaksi sidebar -->
    <script src="js/script.js"></script>

</body>
</html>
