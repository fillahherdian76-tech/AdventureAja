<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

$id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

$product = [
    'name' => '',
    'description' => '',
    'price_per_day' => '',
    'stock' => 0,
    'status' => 'active',
    'image' => ''
];

$isEdit = false;

if ($id) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM products WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header('Location: products.php?pesan=tidak_ditemukan');
        exit;
    }

    $product = $data;
    $isEdit = true;
}

$notice = ($_GET['pesan'] ?? '') === 'validasi'
    ? 'Periksa kembali data yang dimasukkan.'
    : (($_GET['pesan'] ?? '') === 'gambar'
        ? 'Gambar tidak valid. Gunakan JPEG, PNG, atau WebP dengan ukuran maksimal 5 MB.'
        : '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdventureAja | Form Produk</title>
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

            <a href="admin.php" class="nav-link">Dashboard</a>
            <a href="categories.php" class="nav-link">Kategori</a>
            <a href="products.php" class="nav-link active">Produk</a>
            <a href="transaction.php" class="nav-link">Transaksi</a>
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
                <button type="button"
                    class="sidebar-toggle"
                    id="sidebarToggle">Ã¢ËœÂ°</button>

                <div class="page-heading">
                    <p class="breadcrumb">Halaman / Produk / Form</p>
                    <h1>Form Produk</h1>
                </div>
            </div>
        </header>

        <section class="form-intro">
            <p class="eyebrow">MANAJEMEN INVENTARIS</p>

            <h2>
                <?= $isEdit ? 'Edit' : 'Tambah' ?> Produk
            </h2>

            <p>Lengkapi informasi perlengkapan camping.</p>
        </section>

        <section class="form-card">

            <div class="form-card-heading">
                <div class="form-heading-icon">Ã¢â€“Â£</div>
                <div>
                    <h2>Informasi Produk</h2>
                    <p>Isi detail produk. Kategori ditetapkan otomatis dan tidak perlu dipilih.</p>
                </div>
            </div>

            <?php if ($notice): ?>
                <p class="form-note"><?= e($notice) ?></p>
            <?php endif; ?>

            <form method="POST" action="products-process.php" enctype="multipart/form-data">

                <input type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>">

                <input type="hidden"
                    name="productId"
                    value="<?= $isEdit ? (int)$product['id'] : '' ?>">

                <div class="form-group">
                    <label for="name">Nama Produk *</label>
                    <input type="text"
                        id="name"
                        name="name"
                        maxlength="150"
                        value="<?= e($product['name']) ?>"
                        placeholder="Contoh: Tenda 4 Orang"
                        required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description"
                        name="description"
                        rows="4"><?= e($product['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/png,image/webp">
                    <small>Opsional. Format JPG, PNG, atau WebP, maksimal 5 MB. Saat mengedit, biarkan kosong untuk mempertahankan gambar saat ini.</small>
                    <?php if (!empty($product['image'])): ?>
                        <small>Gambar saat ini: <?= e(basename($product['image'])) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="price_per_day">Harga Sewa per Hari *</label>
                    <input type="number"
                        id="price_per_day"
                        name="price_per_day"
                        min="0"
                        step="1000"
                        value="<?= e($product['price_per_day']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="stock">Stok *</label>
                    <input type="number"
                        id="stock"
                        name="stock"
                        min="0"
                        value="<?= (int)$product['stock'] ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="active"
                            <?= $product['status'] === 'active' ? 'selected' : '' ?>>
                            Aktif
                        </option>

                        <option value="inactive"
                            <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>
                            Nonaktif
                        </option>
                    </select>
                </div>

                <p class="form-note">* Wajib diisi.</p>

                <div class="form-actions">
                    <a href="products.php" class="secondary-button">
                        Batal
                    </a>

                    <button type="submit"
                        class="primary-button form-submit">
                        <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' ?>
                    </button>
                </div>

            </form>
        </section>

        <footer class="dashboard-footer">
            <p>Ã‚Â© 2026 AdventureAja.</p>
        </footer>

    </main>
</div>

<script src="js/script.js"></script>
</body>
</html>
