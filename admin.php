
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AdventureAja | Dashboard Admin</title>

    <!-- Menghubungkan file CSS khusus halaman admin -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <!-- Pembungkus seluruh halaman dashboard -->
    <div class="dashboard">

        <!-- Sidebar untuk navigasi halaman admin -->
        <aside class="sidebar" id="sidebar">

            <!-- Logo website -->
            <div class="sidebar-logo">
                <h2>Adventure<span>Aja</span></h2>
                <p>ADMIN PANEL</p>
            </div>

            <!-- Menu navigasi -->
            <nav class="sidebar-nav">
                <p class="menu-label">MENU UTAMA</p>

                <a href="admin.php" class="nav-link active">
                    <span class="nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="categories.php" class="nav-link">
                    <span class="nav-icon">▤</span>
                    <span>Kategori</span>
                </a>

                <a href="transaction.php" class="nav-link">
                    <span class="nav-icon">▣</span>
                    <span>Transaksi</span>
                </a>
            </nav>

            <!-- Informasi admin di bagian bawah sidebar -->
            <div class="sidebar-bottom">
                <div class="admin-avatar">A</div>

                <div class="admin-info">
                    <h4>Administrator</h4>
                    <p>Admin AdventureAja</p>
                </div>
            </div>

        </aside>

        <!-- Konten utama dashboard -->
        <main class="main-content">

            <!-- Header halaman -->
            
<header class="topbar">

    <div class="topbar-left">
        <!-- Tombol untuk membuka atau menutup sidebar -->
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
            <p class="breadcrumb">Halaman / Dashboard</p>
            <h1>Dashboard</h1>
        </div>
    </div>

    <div class="topbar-info">
        <div class="status-indicator"></div>
        <span>Admin Panel</span>
    </div>

</header>


            <!-- Sambutan dashboard -->
            <section class="welcome-section">
                <div class="welcome-text">
                    <p class="eyebrow">ADVENTUREAJA ADMIN PANEL</p>

                    <h2>Selamat datang, Admin!</h2>

                    <p>
                        Kelola perlengkapan camping dan pantau
                        aktivitas penyewaan dengan mudah.
                    </p>

                    <a href="categories.php" class="primary-button">
                        Kelola Kategori
                        <span>→</span>
                    </a>
                </div>

                <div class="welcome-decoration" aria-hidden="true">
                    <span class="decoration-circle"></span>
                    <span class="decoration-icon">⌁</span>
                </div>
            </section>

            <!-- Ringkasan statistik -->
            <section class="statistics-section">
                <div class="section-heading">
                    <div>
                        <h2>Ringkasan Dashboard</h2>
                        <p>Gambaran umum aktivitas penyewaan.</p>
                    </div>
                </div>

                <div class="statistics-grid">

                    <!-- Kartu jumlah kategori -->
                    <article class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon yellow-icon">▤</div>
                            <span class="stat-tag">Data</span>
                        </div>

                        <p class="stat-label">Total Kategori</p>
                        <h3>4</h3>
                        <p class="stat-description">
                            Kategori perlengkapan
                        </p>
                    </article>

                    <!-- Kartu jumlah transaksi -->
                    <article class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon blue-icon">▣</div>
                            <span class="stat-tag">Sewa</span>
                        </div>

                        <p class="stat-label">Total Transaksi</p>
                        <h3>12</h3>
                        <p class="stat-description">
                            Seluruh data transaksi
                        </p>
                    </article>

                    <!-- Kartu transaksi yang diproses -->
                    <article class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon green-icon">◷</div>
                            <span class="stat-tag">Proses</span>
                        </div>

                        <p class="stat-label">Sedang Diproses</p>
                        <h3>3</h3>
                        <p class="stat-description">
                            Transaksi perlu ditangani
                        </p>
                    </article>

                </div>
            </section>

            <!-- Ringkasan transaksi terbaru -->
            <section class="transactions-section">
                <div class="section-heading">
                    <div>
                        <h2>Transaksi Terbaru</h2>
                        <p>Contoh aktivitas penyewaan terbaru.</p>
                    </div>

                    <a href="transaction.php" class="text-link">
                        Lihat Semua →
                    </a>
                </div>

                <div class="table-container">
                    <table class="transaction-table">

                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Perlengkapan</th>
                                <th>Tanggal Sewa</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">R</div>
                                        <span>Rizky Pratama</span>
                                    </div>
                                </td>
                                <td>Tenda 3 Layer</td>
                                <td>20 Okt 2026</td>
                                <td>Rp150.000</td>
                                <td>
                                    <span class="status-badge status-process">
                                        Diproses
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">D</div>
                                        <span>Dinda Putri</span>
                                    </div>
                                </td>
                                <td>Carrier 60L</td>
                                <td>21 Okt 2026</td>
                                <td>Rp100.000</td>
                                <td>
                                    <span class="status-badge status-complete">
                                        Selesai
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">A</div>
                                        <span>Andi Saputra</span>
                                    </div>
                                </td>
                                <td>Sleeping Bag</td>
                                <td>22 Okt 2026</td>
                                <td>Rp50.000</td>
                                <td>
                                    <span class="status-badge status-pending">
                                        Menunggu
                                    </span>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </section>

            <!-- Footer dashboard -->
            <footer class="dashboard-footer">
                <p>© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p>
            </footer>

        </main>
    </div>
    
    <script src="js/script.js"></script>

</body>
</html>
