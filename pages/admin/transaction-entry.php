<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

$usersResult = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'user' ORDER BY name");
$productsResult = mysqli_query($conn, "SELECT id, name, price_per_day, stock FROM products WHERE status = 'active' AND stock > 0 ORDER BY name");

if (!$usersResult || !$productsResult) {
    error_log(mysqli_error($conn));
    exit('Data pelanggan atau produk gagal dimuat.');
}

$users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);
$products = mysqli_fetch_all($productsResult, MYSQLI_ASSOC);
$selectedProductId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT) ?: 0;
$notice = ($_GET['pesan'] ?? '') === 'validasi'
    ? 'Periksa kembali data. Pastikan tanggal dan stok produk tersedia.'
    : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdventureAja | Tambah Transaksi</title>
    <link rel="icon" href="assets/AdventureAja.png">
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime(APP_ROOT . '/css/admin.css') ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
                <div class="page-heading"><p class="breadcrumb">Halaman / Transaksi / Form</p><h1>Tambah Transaksi</h1></div>
            </div>
            <div class="topbar-info"><div class="status-indicator"></div><span>Admin Panel</span></div>
        </header>

        <section class="page-heading">
            <div><p class="eyebrow">MANAJEMEN PENYEWAAN</p><h1>Tambah Transaksi</h1><p class="page-description">Pilih pelanggan dan produk. Total dihitung dari harga serta lama sewa.</p></div>
        </section>

        <section class="form-card">
            <div class="form-card-header"><div><h2>Informasi Transaksi</h2><p>Pesanan baru akan tersimpan di database dengan status menunggu konfirmasi.</p></div><i class="bx bxs-receipt form-header-icon"></i></div>

            <?php if ($notice !== ''): ?><p class="form-note" role="alert"><?= e($notice) ?></p><?php endif; ?>

            <?php if (!$users): ?>
                <p class="form-note">Belum ada akun pelanggan. Buat akun pelanggan terlebih dahulu.</p>
            <?php elseif (!$products): ?>
                <p class="form-note">Belum ada produk aktif dengan stok tersedia.</p>
            <?php else: ?>
                <form method="POST" action="transaction-process.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="create">

                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label for="user_id">Pelanggan</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">Pilih pelanggan</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= (int)$user['id'] ?>"><?= e($user['name']) ?> (<?= e($user['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label for="product_id">Perlengkapan Camping</label>
                            <select id="product_id" name="product_id" required>
                                <option value="" data-price="0" data-stock="0">Pilih perlengkapan</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= (int)$product['id'] ?>" data-price="<?= (float)$product['price_per_day'] ?>" data-stock="<?= (int)$product['stock'] ?>" <?= $selectedProductId === (int)$product['id'] ? 'selected' : '' ?>>
                                        <?= e($product['name']) ?> Ã¢â‚¬â€ Rp<?= number_format((float)$product['price_per_day'], 0, ',', '.') ?>/hari (stok <?= (int)$product['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Jumlah Barang</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="form-group">
                            <label for="rental_start_date">Tanggal Mulai Sewa</label>
                            <input type="date" id="rental_start_date" name="rental_start_date" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rental_end_date">Tanggal Pengembalian</label>
                            <input type="date" id="rental_end_date" name="rental_end_date" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group form-group-full"><p id="totalPreview" class="form-note" aria-live="polite">Pilih produk dan tanggal untuk melihat estimasi biaya.</p></div>
                    </div>

                    <div class="form-actions">
                        <a href="transaction.php" class="btn-secondary">Batal</a>
                        <button type="submit" class="primary-button form-submit">Simpan Transaksi</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>

        <footer class="dashboard-footer"><p>Ã‚Â© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p></footer>
    </main>
</div>
<script src="js/script.js"></script>
<script>
const productField = document.getElementById('product_id');
const quantityField = document.getElementById('quantity');
const startField = document.getElementById('rental_start_date');
const endField = document.getElementById('rental_end_date');
const totalPreview = document.getElementById('totalPreview');

function updateTransactionTotal() {
    if (!productField || !quantityField || !startField || !endField || !totalPreview) return;
    const option = productField.options[productField.selectedIndex];
    const price = Number(option.dataset.price || 0);
    const quantity = Math.max(1, Number(quantityField.value || 1));
    const stock = Number(option.dataset.stock || 0);
    quantityField.max = stock || '';

    if (!startField.value || !endField.value || !price) {
        totalPreview.textContent = 'Pilih produk dan tanggal untuk melihat estimasi biaya.';
        return;
    }

    const start = new Date(startField.value + 'T00:00:00');
    const end = new Date(endField.value + 'T00:00:00');
    const days = Math.max(1, Math.round((end - start) / 86400000));
    if (end < start) {
        totalPreview.textContent = 'Tanggal pengembalian tidak boleh sebelum tanggal mulai.';
        return;
    }
    totalPreview.textContent = 'Estimasi total: Rp' + new Intl.NumberFormat('id-ID').format(price * quantity * days) + ' (' + days + ' hari).';
}

[productField, quantityField, startField, endField].forEach(function(field) {
    field?.addEventListener('input', updateTransactionTotal);
    field?.addEventListener('change', updateTransactionTotal);
});
startField?.addEventListener('change', function() {
    endField.min = startField.value || '<?= date('Y-m-d') ?>';
    if (endField.value && endField.value < startField.value) endField.value = startField.value;
    updateTransactionTotal();
});
updateTransactionTotal();
</script>
</body>
</html>
