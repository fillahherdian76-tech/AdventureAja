<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/config/koneksi.php';

$id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$category = ['name' => '', 'description' => ''];
$isEdit = false;

if ($id) {
    $stmt = mysqli_prepare($conn, 'SELECT id, name, description FROM categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $category = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$category) {
        header('Location: categories.php?pesan=tidak_ditemukan');
        exit;
    }
    $isEdit = true;
}
$notice = [
    'duplikat' => 'Nama kategori sudah digunakan.',
    'validasi' => 'Nama dan deskripsi wajib diisi. Nama maksimal 100 karakter.'
][$_GET['pesan'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>AdventureAja | Form Kategori</title><link rel="stylesheet" href="css/admin.css"></head>
<body><div class="dashboard">
<aside class="sidebar" id="sidebar"><div class="sidebar-logo"><h2>Adventure<span>Aja</span></h2><p>ADMIN PANEL</p></div><nav class="sidebar-nav"><p class="menu-label">MENU UTAMA</p><a href="admin.php" class="nav-link"><span class="nav-icon">▦</span><span>Dashboard</span></a><a href="categories.php" class="nav-link active"><span class="nav-icon">▤</span><span>Kategori</span></a><a href="transaction.php" class="nav-link"><span class="nav-icon">▣</span><span>Transaksi</span></a></nav><div class="sidebar-bottom"><div class="admin-avatar">A</div><div class="admin-info"><h4><?= e($_SESSION['user']['name']) ?></h4><p>Administrator</p><a href="logout.php">Keluar</a></div></div></aside>
<main class="main-content"><header class="topbar"><div class="topbar-left"><button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Buka atau tutup sidebar" aria-controls="sidebar" aria-expanded="true">☰</button><div class="page-heading"><p class="breadcrumb">Halaman / Kategori / Form</p><h1>Form Kategori</h1></div></div><div class="topbar-info"><div class="status-indicator"></div><span>Admin Panel</span></div></header>
<section class="form-intro"><p class="eyebrow">MANAJEMEN DATA</p><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> Kategori Perlengkapan</h2><p>Isi informasi kategori perlengkapan camping yang ingin <?= $isEdit ? 'diperbarui' : 'ditambahkan' ?>.</p></section>
<section class="form-card"><div class="form-card-heading"><div class="form-heading-icon">▤</div><div><h2>Informasi Kategori</h2><p>Lengkapi kolom di bawah ini.</p></div></div>
<?php if ($notice !== ''): ?><p class="form-note" role="alert"><?= e($notice) ?></p><?php endif; ?>
<form method="POST" action="categories-process.php">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="categoryId" value="<?= $isEdit ? (int) $category['id'] : '' ?>">
<div class="form-group"><label for="categoryName">Nama Kategori <span class="required-mark">*</span></label><input type="text" id="categoryName" name="categoryName" maxlength="100" placeholder="Contoh: Tenda" value="<?= e($category['name']) ?>" required></div>
<div class="form-group"><label for="categoryDescription">Deskripsi <span class="required-mark">*</span></label><textarea id="categoryDescription" name="categoryDescription" rows="4" placeholder="Jelaskan jenis perlengkapan dalam kategori ini..." required><?= e($category['description']) ?></textarea><small>Berikan deskripsi singkat dan jelas.</small></div>
<p class="form-note"><span class="required-mark">*</span> Wajib diisi.</p>
<div class="form-actions"><a href="categories.php" class="secondary-button">Batal</a><button type="submit" class="primary-button form-submit"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' ?></button></div>
</form></section><footer class="dashboard-footer"><p>© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p></footer></main></div>
<script src="js/script.js"></script></body></html>