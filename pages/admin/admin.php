<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();

require_once APP_ROOT . '/config/koneksi.php';

// ======================================
// STATISTIK DASHBOARD
// ======================================

// Total kategori
$resultCategories = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM categories"
);
$totalCategories = (int) mysqli_fetch_assoc($resultCategories)['total'];

// Total produk
$resultProducts = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);
$totalProducts = (int) mysqli_fetch_assoc($resultProducts)['total'];

// Total transaksi
$resultTransactions = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM transactions"
);
$totalTransactions = (int) mysqli_fetch_assoc($resultTransactions)['total'];

// Transaksi pending dan aktif
$resultProcess = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM transactions
     WHERE status IN ('pending', 'active')"
);
$totalProcess = (int) mysqli_fetch_assoc($resultProcess)['total'];

// ======================================
// TRANSAKSI TERBARU DARI DATABASE
// ======================================

$sqlLatest = "
    SELECT
        t.id,
        t.transaction_code,
        t.rental_start_date,
        t.total_amount,
        t.status,
        u.name AS customer_name,
        GROUP_CONCAT(
            DISTINCT p.name
            ORDER BY p.name
            SEPARATOR ', '
        ) AS product_names
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN transaction_details td
        ON t.id = td.transaction_id
    LEFT JOIN products p
        ON td.product_id = p.id
    GROUP BY
        t.id,
        t.transaction_code,
        t.rental_start_date,
        t.total_amount,
        t.status,
        u.name
    ORDER BY t.created_at DESC
    LIMIT 5
";

$resultLatest = mysqli_query($conn, $sqlLatest);

if (!$resultLatest) {
    error_log(mysqli_error($conn));
    exit('Gagal mengambil transaksi terbaru.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AdventureAja | Dashboard Admin</title>

    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime(APP_ROOT . '/css/admin.css') ?>">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">
            <h2>Adventure<span>Aja</span></h2>
            <p>ADMIN PANEL</p>
        </div>

        <nav class="sidebar-nav">
            <p class="menu-label">MENU UTAMA</p>

            <a href="admin.php" class="nav-link active">
                <span class="nav-icon">Ã¢â€“Â¦</span>
                <span>Dashboard</span>
            </a>

            <a href="categories.php" class="nav-link">
                <span class="nav-icon">Ã¢â€“Â¤</span>
                <span>Kategori</span>
            </a>

            <a href="products.php" class="nav-link">
                <span class="nav-icon">Ã¢â€“Â£</span>
                <span>Produk</span>
            </a>

            <a href="transaction.php" class="nav-link">
                <span class="nav-icon">Ã¢â€“Â£</span>
                <span>Transaksi</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="admin-avatar">A</div>

            <div class="admin-info">
                <h4><?= e($_SESSION['user']['name'] ?? 'Administrator') ?></h4>
                <p>Admin AdventureAja</p>
                <a href="logout.php" class="admin-logout">Keluar</a>
            </div>
        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="sidebar-toggle"
                    id="sidebarToggle"
                    aria-label="Buka atau tutup sidebar"
                    aria-controls="sidebar"
                    aria-expanded="true">
                    Ã¢ËœÂ°
                </button>

                <div class="page-heading">
                    <p class="breadcrumb">Halaman / Dashboard</p>
                    <h1>Dashboard</h1>
                </div>

            </div>

            <div class="topbar-info">
                <div class="status-indicator"></div>
                <span>Halo <?= e($_SESSION['user']['name'] ?? 'Admin') ?></span>
            </div>

        </header>

        <!-- SAMBUTAN -->
        <section class="welcome-section">

            <div class="welcome-text">

                <p class="eyebrow">ADVENTUREAJA ADMIN PANEL</p>

                <h2>
                    Selamat datang,
                    <?= e($_SESSION['user']['name'] ?? 'Admin') ?>!
                </h2>

                <p>
                    Kelola perlengkapan camping dan pantau
                    aktivitas penyewaan dengan mudah.
                </p>

                <a href="categories.php" class="primary-button">
                    Kelola Kategori
                    <span>Ã¢â€ â€™</span>
                </a>

            </div>

            <div class="welcome-decoration" aria-hidden="true">
                <span class="decoration-circle"></span>
                <span class="decoration-icon">Ã¢Å’Â</span>
            </div>

        </section>

        <!-- STATISTIK -->
        <section class="statistics-section">

            <div class="section-heading">
                <div>
                    <h2>Ringkasan Dashboard</h2>
                    <p>Gambaran umum aktivitas penyewaan.</p>
                </div>
            </div>

            <div class="statistics-grid">

                <!-- TOTAL KATEGORI -->
                <article class="stat-card">

                    <div class="stat-card-top">
                        <div class="stat-icon yellow-icon">Ã¢â€“Â¤</div>
                        <span class="stat-tag">Data</span>
                    </div>

                    <p class="stat-label">Total Kategori</p>

                    <h3><?= $totalCategories ?></h3>

                    <p class="stat-description">
                        Kategori perlengkapan
                    </p>

                </article>

                <!-- TOTAL PRODUK -->
                <article class="stat-card">

                    <div class="stat-card-top">
                        <div class="stat-icon green-icon">Ã¢â€“Â£</div>
                        <span class="stat-tag">Produk</span>
                    </div>

                    <p class="stat-label">Total Produk</p>

                    <h3><?= $totalProducts ?></h3>

                    <p class="stat-description">
                        Perlengkapan camping
                    </p>

                </article>

                <!-- TOTAL TRANSAKSI -->
                <article class="stat-card">

                    <div class="stat-card-top">
                        <div class="stat-icon blue-icon">Ã¢â€“Â£</div>
                        <span class="stat-tag">Sewa</span>
                    </div>

                    <p class="stat-label">Total Transaksi</p>

                    <h3><?= $totalTransactions ?></h3>

                    <p class="stat-description">
                        Seluruh data transaksi
                    </p>

                </article>

                <!-- TRANSAKSI DIPROSES -->
                <article class="stat-card">

                    <div class="stat-card-top">
                        <div class="stat-icon green-icon">Ã¢â€”Â·</div>
                        <span class="stat-tag">Proses</span>
                    </div>

                    <p class="stat-label">Sedang Diproses</p>

                    <h3><?= $totalProcess ?></h3>

                    <p class="stat-description">
                        Transaksi pending dan aktif
                    </p>

                </article>

            </div>

        </section>

        <!-- TRANSAKSI TERBARU -->
        <section class="transactions-section">

            <div class="section-heading">

                <div>
                    <h2>Transaksi Terbaru</h2>
                    <p>Data aktivitas penyewaan dari database.</p>
                </div>

                <a href="transaction.php" class="text-link">
                    Lihat Semua Ã¢â€ â€™
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

                    <?php if (mysqli_num_rows($resultLatest) === 0): ?>

                        <tr>
                            <td colspan="5">
                                Belum ada transaksi.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php while ($transaction = mysqli_fetch_assoc($resultLatest)): ?>

                            <?php
                            $statusLabels = [
                                'pending' => 'Menunggu',
                                'active' => 'Diproses',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan'
                            ];

                            $statusClasses = [
                                'pending' => 'status-pending',
                                'active' => 'status-process',
                                'completed' => 'status-complete',
                                'cancelled' => 'status-pending'
                            ];

                            $status = $transaction['status'];
                            ?>

                            <tr>

                                <td>
                                    <div class="customer-cell">

                                        <div class="customer-avatar">
                                            <?= e(
                                                mb_strtoupper(
                                                    mb_substr(
                                                        $transaction['customer_name'] ?? 'U',
                                                        0,
                                                        1
                                                    )
                                                )
                                            ) ?>
                                        </div>

                                        <span>
                                            <?= e($transaction['customer_name'] ?? 'Pelanggan') ?>
                                        </span>

                                    </div>
                                </td>

                                <td>
                                    <?= e($transaction['product_names'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd M Y',
                                        strtotime($transaction['rental_start_date'])
                                    ) ?>
                                </td>

                                <td>
                                    Rp<?= number_format(
                                        (float)$transaction['total_amount'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <span class="status-badge <?= e(
                                        $statusClasses[$status] ?? 'status-pending'
                                    ) ?>">
                                        <?= e($statusLabels[$status] ?? $status) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <!-- FOOTER -->
        <footer class="dashboard-footer">
            <p>
                Ã‚Â© 2026 AdventureAja.
                Temani setiap langkah petualanganmu.
            </p>
        </footer>

    </main>

</div>

<script src="js/script.js"></script>

</body>
</html>
