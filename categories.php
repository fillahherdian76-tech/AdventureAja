<?php
require_once __DIR__ . '/auth.php';
require_admin();
require_once __DIR__ . '/config/koneksi.php';

$sql = "SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id, c.name, c.description
        ORDER BY c.id ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log(mysqli_error($conn));
    exit('Data kategori gagal dimuat.');
}
$message = [
    'ditambahkan' => 'Kategori berhasil ditambahkan.',
    'diperbarui' => 'Kategori berhasil diperbarui.',
    'dihapus' => 'Kategori berhasil dihapus.',
    'masih_digunakan' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.',
    'duplikat' => 'Nama kategori sudah digunakan.',
    'gagal' => 'Proses kategori gagal.',
    'id_tidak_valid' => 'ID kategori tidak valid.',
    'tidak_ditemukan' => 'Kategori tidak ditemukan.'
][$_GET['pesan'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AdventureAja | Kategori</title>
<link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="dashboard">
<aside class="sidebar" id="sidebar">
<div class="sidebar-logo"><h2>Adventure<span>Aja</span></h2><p>ADMIN PANEL</p></div>
<nav class="sidebar-nav"><p class="menu-label">MENU UTAMA</p>
<a href="admin.php" class="nav-link"><span class="nav-icon">▦</span><span>Dashboard</span></a>
<a href="categories.php" class="nav-link active"><span class="nav-icon">▤</span><span>Kategori</span></a>
<a href="transaction.php" class="nav-link"><span class="nav-icon">▣</span><span>Transaksi</span></a>
</nav>
<div class="sidebar-bottom"><div class="admin-avatar">A</div><div class="admin-info"><h4><?= e($_SESSION['user']['name']) ?></h4><p>Administrator</p><a href="logout.php">Keluar</a></div></div>
</aside>
<main class="main-content">
<header class="topbar"><div class="topbar-left"><button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Buka atau tutup sidebar" aria-controls="sidebar" aria-expanded="true">☰</button><div class="page-heading"><p class="breadcrumb">Halaman / Kategori</p><h1>Kategori Perlengkapan</h1></div></div><div class="topbar-info"><div class="status-indicator"></div><span>Admin Panel</span></div></header>
<section class="welcome-section category-welcome"><div class="welcome-text"><p class="eyebrow">MANAJEMEN PERLENGKAPAN</p><h2>Kelola Kategori Camping</h2><p>Atur data kategori perlengkapan agar inventaris AdventureAja lebih terorganisasi.</p></div></section>
<section class="category-section">
<div class="section-heading"><div><h2>Data Kategori</h2><p>Daftar kategori perlengkapan dari database.</p></div><a href="categories-entry.php" class="primary-button add-button"><span>+</span>Tambah Kategori</a></div>
<?php if ($message !== ''): ?><p class="form-note" role="status"><?= e($message) ?></p><?php endif; ?>
<div class="table-container"><table class="transaction-table category-table">
<thead><tr><th>No.</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Barang</th><th>Aksi</th></tr></thead>
<tbody>
<?php if (mysqli_num_rows($result) === 0): ?><tr><td colspan="5">Belum ada kategori.</td></tr>
<?php else: $no = 1; while ($category = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $no++ ?></td>
<td><div class="category-name"><div class="category-symbol"><?= e(mb_strtoupper(mb_substr($category['name'], 0, 1))) ?></div><span><?= e($category['name']) ?></span></div></td>
<td><?= e($category['description'] ?? '-') ?></td>
<td><?= (int) $category['product_count'] ?> barang</td>
<td><div class="action-buttons"><a class="action-button edit-button" href="categories-entry.php?edit=<?= (int) $category['id'] ?>">Edit</a>
<form method="POST" action="categories-process.php" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" style="display:inline">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="categoryId" value="<?= (int) $category['id'] ?>">
<button type="submit" class="action-button delete-button">Hapus</button></form></div></td>
</tr>
<?php endwhile; endif; ?>
</tbody></table></div>
<div class="table-footer"><p>Menampilkan <?= mysqli_num_rows($result) ?> kategori perlengkapan</p><span class="table-page-number">Halaman 1</span></div>
</section>
<footer class="dashboard-footer"><p>© 2026 AdventureAja. Temani setiap langkah petualanganmu.</p></footer>
</main></div>
<script>
document.getElementById("sidebarToggle")?.addEventListener("click", function () {
 const sidebar = document.getElementById("sidebar");
 sidebar?.classList.toggle("active");
 this.setAttribute("aria-expanded", sidebar?.classList.contains("active") ? "true" : "false");
});
</script>
</body></html>