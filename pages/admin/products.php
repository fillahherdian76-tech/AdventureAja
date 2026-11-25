<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

$sql = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log(mysqli_error($conn));
    exit('Gagal mengambil data produk.');
}

$totalProducts = mysqli_num_rows($result);

$messages = [
    'ditambahkan' => 'Produk berhasil ditambahkan.',
    'diperbarui' => 'Produk berhasil diperbarui.',
    'dihapus' => 'Produk berhasil dihapus.',
    'gagal' => 'Terjadi kesalahan saat memproses produk.',
    'tidak_ditemukan' => 'Produk tidak ditemukan.',
    'validasi' => 'Periksa kembali data produk.',
];

$message = $messages[$_GET['pesan'] ?? ''] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdventureAja | Produk</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime(APP_ROOT . '/css/admin.css') ?>">
</head>

<body>

    <div class="dashboard">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <h2>Adventure<span>Aja</span></h2>
                <p>ADMIN PANEL</p>
            </div>

            <nav class="sidebar-nav">
                <p class="menu-label">MENU UTAMA</p>

                <a href="admin.php" class="nav-link">
                    <span class="nav-icon">&#8962;</span>
                    <span>Dashboard</span>
                </a>

                <a href="categories.php" class="nav-link">
                    <span class="nav-icon">&#9638;</span>
                    <span>Kategori</span>
                </a>

                <a href="products.php" class="nav-link active">
                    <span class="nav-icon">&#9635;</span>
                    <span>Produk</span>
                </a>

                <a href="transaction.php" class="nav-link">
                    <span class="nav-icon">&#128203;</span>
                    <span>Transaksi</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="admin-avatar">A</div>
                <div class="admin-info">
                    <h4><?= e($_SESSION['user']['name']) ?></h4>
                    <p>Administrator</p>
                    <a href="logout.php" class="admin-logout">Keluar</a>
                </div>
            </div>
        </aside>

        <main class="main-content">

            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Buka atau tutup sidebar" aria-controls="sidebar" aria-expanded="true">&#9776;</button>

                    <div class="page-heading">
                        <p class="breadcrumb">Halaman / Produk</p>
                        <h1>Produk Perlengkapan</h1>
                    </div>
                </div>

                <div class="topbar-info">
                    <div class="status-indicator"></div>
                    <span>Admin Panel</span>
                </div>
            </header>

            <section class="welcome-section">
                <div class="welcome-text">
                    <p class="eyebrow">MANAJEMEN INVENTARIS</p>
                    <h2>Kelola Produk Camping</h2>
                    <p>
                        Tambahkan, edit, dan hapus perlengkapan
                        camping AdventureAja.
                    </p>
                </div>
            </section>

            <section class="category-section">

                <div class="section-heading">
                    <div>
                        <h2>Data Produk</h2>
                        <p>Jumlah produk: <?= $totalProducts ?></p>
                    </div>

                    <a href="products-entry.php"
                        class="primary-button">
                        + Tambah Produk
                    </a>

                    <a href="laporan-produk.php"
                        class="primary-button"
                        target="_blank">
                        Cetak PDF
                    </a>
                </div>

                <?php if ($message): ?>
                    <p class="form-note" role="status">
                        <?= e($message) ?>
                    </p>
                <?php endif; ?>

                <div class="table-container">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga/Hari</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($totalProducts === 0): ?>
                                <tr>
                                    <td colspan="7">Belum ada produk.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>

                                        <td><?= e($product['name']) ?></td>

                                        <td>
                                            <?= e($product['category_name'] ?? '-') ?>
                                        </td>

                                        <td>
                                            Rp<?= number_format(
                                                    (float)$product['price_per_day'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>
                                        </td>

                                        <td><?= (int)$product['stock'] ?></td>

                                        <td>
                                            <?= $product['status'] === 'active'
                                                ? 'Aktif' : 'Nonaktif' ?>
                                        </td>

                                        <td>
                                            <div class="action-buttons">
                                                <a class="action-button edit-button"
                                                    href="products-entry.php?edit=<?= (int)$product['id'] ?>">
                                                    Edit
                                                </a>

                                                <form method="POST"
                                                    action="products-process.php"
                                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?');">

                                                    <input type="hidden"
                                                        name="csrf_token"
                                                        value="<?= e(csrf_token()) ?>">

                                                    <input type="hidden"
                                                        name="action"
                                                        value="delete">

                                                    <input type="hidden"
                                                        name="productId"
                                                        value="<?= (int)$product['id'] ?>">

                                                    <button type="submit"
                                                        class="action-button delete-button">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <p>Menampilkan <?= $totalProducts ?> produk.</p>
                </div>

            </section>

            <footer class="dashboard-footer">
                <p>&copy; 2026 AdventureAja.</p>
            </footer>

        </main>
    </div>

    <script src="js/script.js"></script>
</body>

</html>
