<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

$sql = "
    SELECT
        t.id,
        t.transaction_code,
        t.rental_start_date,
        t.rental_end_date,
        t.total_amount,
        t.status,
        t.created_at,
        u.name AS customer_name,
        GROUP_CONCAT(
            CONCAT(p.name, ' (', td.quantity, ')')
            ORDER BY p.name SEPARATOR ', '
        ) AS product_names
    FROM transactions t
    LEFT JOIN users u ON u.id = t.user_id
    LEFT JOIN transaction_details td ON td.transaction_id = t.id
    LEFT JOIN products p ON p.id = td.product_id
    GROUP BY
        t.id, t.transaction_code, t.rental_start_date,
        t.rental_end_date, t.total_amount, t.status,
        t.created_at, u.name
    ORDER BY t.created_at DESC, t.id DESC
";
$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log(mysqli_error($conn));
    exit('Data transaksi gagal dimuat.');
}

$transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
$statusCounts = ['pending' => 0, 'active' => 0, 'completed' => 0];
foreach ($transactions as $transaction) {
    if (isset($statusCounts[$transaction['status']])) {
        $statusCounts[$transaction['status']]++;
    }
}

$statusLabels = [
    'pending' => 'Menunggu Konfirmasi',
    'active' => 'Sedang Disewa',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
];
$messages = [
    'ditambahkan' => 'Transaksi berhasil ditambahkan.',
    'diperbarui' => 'Status transaksi berhasil diperbarui.',
    'dihapus' => 'Transaksi berhasil dihapus.',
    'stok' => 'Status tidak dapat diaktifkan karena stok bertabrakan dengan pesanan lain.',
    'gagal' => 'Transaksi gagal diproses.',
];
$message = $messages[$_GET['pesan'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdventureAja | Transaksi</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime(APP_ROOT . '/css/admin.css') ?>">
    <style>
        .transaction-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:15px; margin:28px 0; }
        .summary-card { padding:20px; background:var(--navy); border:1px solid var(--border-color); border-radius:14px; }
        .summary-card p { color:var(--text-muted); font-size:12px; }
        .summary-card h2 { margin-top:10px; font-size:26px; }
        .status-form { display:flex; align-items:center; gap:7px; }
        .status-form select, .status-form button, .delete-form button { padding:7px 9px; border:1px solid var(--border-color); border-radius:7px; background:var(--navy-dark); color:var(--white); font:inherit; font-size:11px; }
        .status-form button { cursor:pointer; color:var(--yellow); }
        .delete-form { margin-top:7px; }
        .delete-form button { cursor:pointer; color:#ff9292; }
        .transaction-message { margin:16px 0; padding:12px 15px; border:1px solid var(--border-color); border-radius:9px; color:var(--text-light); }
        @media(max-width:700px) { .transaction-summary { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    </style>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo"><h2>Adventure<span>Aja</span></h2><p>ADMIN PANEL</p></div>
        <nav class="sidebar-nav">
            <p class="menu-label">MENU UTAMA</p>
            <a href="admin.php" class="nav-link"><span class="nav-icon">Ã¢â€“Â¦</span><span>Dashboard</span></a>
            <a href="categories.php" class="nav-link"><span class="nav-icon">Ã¢â€“Â¤</span><span>Kategori</span></a>
            <a href="products.php" class="nav-link"><span class="nav-icon">Ã¢â€“Â£</span><span>Produk</span></a>
            <a href="transaction.php" class="nav-link active"><span class="nav-icon">Ã¢â€“Â£</span><span>Transaksi</span></a>
        </nav>
        <div class="sidebar-bottom"><div class="admin-avatar">A</div><div class="admin-info"><h4><?= e($_SESSION['user']['name'] ?? 'Administrator') ?></h4><p>Admin AdventureAja</p><a href="logout.php" class="admin-logout">Keluar</a></div></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Buka atau tutup sidebar" aria-controls="sidebar" aria-expanded="true">Ã¢ËœÂ°</button>
                <div class="page-heading"><p class="breadcrumb">Halaman / Transaksi</p><h1>Manajemen Transaksi</h1></div>
            </div>
            <div class="topbar-info"><div class="status-indicator"></div><span>Admin Panel</span></div>
        </header>

        <section class="welcome-section transaction-welcome">
            <div class="welcome-text"><p class="eyebrow">MANAJEMEN PENYEWAAN</p><h2>Pantau Transaksi Penyewaan</h2><p>Pesanan user otomatis tercatat di sini setelah memilih jadwal dan menekan Ã¢â‚¬Å“Buat PesananÃ¢â‚¬Â pada checkout.</p></div>
        </section>

        <section class="transaction-summary" aria-label="Ringkasan transaksi">
            <article class="summary-card"><p>Total Transaksi</p><h2><?= count($transactions) ?></h2></article>
            <article class="summary-card"><p>Menunggu Konfirmasi</p><h2><?= $statusCounts['pending'] ?></h2></article>
            <article class="summary-card"><p>Sedang Disewa</p><h2><?= $statusCounts['active'] ?></h2></article>
            <article class="summary-card"><p>Selesai</p><h2><?= $statusCounts['completed'] ?></h2></article>
        </section>

        <section class="transaction-management">
            <div class="section-heading">
                <div><h2>Daftar Transaksi</h2><p><?= count($transactions) ?> transaksi tersimpan di database.</p></div>
                <a href="transaction-entry.php" class="primary-button add-button"><span>+</span> Tambah Manual</a>
            </div>

            <?php if ($message !== ''): ?><p class="transaction-message" role="status"><?= e($message) ?></p><?php endif; ?>

            <div class="table-container">
                <table class="transaction-table">
                    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Perlengkapan</th><th>Tanggal Sewa</th><th>Total Biaya</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php if (!$transactions): ?>
                        <tr><td colspan="7">Belum ada transaksi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="transaction-id"><?= e($transaction['transaction_code']) ?></td>
                                <td><?= e($transaction['customer_name'] ?? 'Pengguna') ?></td>
                                <td><?= e($transaction['product_names'] ?: 'Detail tidak tersedia') ?></td>
                                <td><?= e(date('d M Y', strtotime($transaction['rental_start_date']))) ?> Ã¢â‚¬â€œ <?= e(date('d M Y', strtotime($transaction['rental_end_date']))) ?></td>
                                <td>Rp<?= number_format((float)$transaction['total_amount'], 0, ',', '.') ?></td>
                                <?php
                                $statusClass = [
                                    'pending' => 'status-pending',
                                    'active' => 'status-process',
                                    'completed' => 'status-complete',
                                    'cancelled' => 'status-inactive',
                                ][$transaction['status']] ?? 'status-pending';
                                ?>
                                <td><span class="status-badge <?= e($statusClass) ?>"><?= e($statusLabels[$transaction['status']] ?? $transaction['status']) ?></span></td>
                                <td>
                                    <form class="status-form" method="POST" action="transaction-process.php">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="status">
                                        <input type="hidden" name="transaction_id" value="<?= (int)$transaction['id'] ?>">
                                        <select name="status" aria-label="Status transaksi <?= e($transaction['transaction_code']) ?>">
                                            <?php foreach ($statusLabels as $value => $label): ?>
                                                <option value="<?= e($value) ?>" <?= $transaction['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit">Simpan</button>
                                    </form>
                                    <form class="delete-form" method="POST" action="transaction-process.php" onsubmit="return confirm('Hapus transaksi ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="transaction_id" value="<?= (int)$transaction['id'] ?>">
                                        <button type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="dashboard-footer"><p>Ã‚Â© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p></footer>
    </main>
</div>
<script src="js/script.js"></script>
</body>
</html>
