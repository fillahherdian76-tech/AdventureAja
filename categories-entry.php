
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AdventureAja | Form Kategori</title>

    <!-- Menggunakan stylesheet yang sama dengan halaman Admin -->
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
                        <p class="breadcrumb">Halaman / Kategori / Form</p>
                        <h1>Form Kategori</h1>
                    </div>
                </div>

                <div class="topbar-info">
                    <div class="status-indicator"></div>
                    <span>Admin Panel</span>
                </div>

            </header>

            <!-- Pengantar form -->
            <section class="form-intro">
                <p class="eyebrow">MANAJEMEN DATA</p>
                <h2>Tambah Kategori Perlengkapan</h2>
                <p>
                    Isi informasi kategori perlengkapan camping
                    yang ingin ditambahkan ke AdventureAja.
                </p>
            </section>

            <!-- Form kategori -->
            <section class="form-card">

                <div class="form-card-heading">
                    <div class="form-heading-icon">▤</div>

                    <div>
                        <h2>Informasi Kategori</h2>
                        <p>Lengkapi kolom di bawah ini.</p>
                    </div>
                </div>

                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="categoryId">

                    <!-- Nama kategori -->
                    <div class="form-group">
                        <label for="categoryName">
                            Nama Kategori
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="categoryName"
                            name="categoryName"
                            placeholder="Contoh: Tenda"
                            required
                        >

                        <button
                            type="submit"
                            class="primary-button form-submit"
                            id="categorySubmitButton">
                            Simpan Kategori
                        </button>
                    </div>

                    <!-- Deskripsi kategori -->
                    <div class="form-group">
                        <label for="categoryDescription">
                            Deskripsi
                            <span class="required-mark">*</span>
                        </label>

                        <textarea
                            id="categoryDescription"
                            name="categoryDescription"
                            rows="4"
                            placeholder="Jelaskan jenis perlengkapan dalam kategori ini..."
                            required
                        ></textarea>

                        <small>
                            Berikan deskripsi singkat dan jelas.
                        </small>
                    </div>

                    <!-- Status kategori -->
                    <div class="form-group">
                        <label for="categoryStatus">
                            Status Kategori
                        </label>

                        <select
                            id="categoryStatus"
                            name="categoryStatus"
                        >
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>

                        <small>
                            Kategori nonaktif dapat ditandai agar
                            tidak digunakan dalam pengelolaan katalog.
                        </small>
                    </div>

                    <!-- Keterangan kolom wajib -->
                    <p class="form-note">
                        <span class="required-mark">*</span>
                        Wajib diisi.
                    </p>

                    <!-- Tombol aksi -->
                    <div class="form-actions">
                        <a
                            href="categories.php"
                            class="secondary-button"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="primary-button form-submit"
                        >
                            Simpan Kategori
                        </button>
                    </div>

                </form>

            </section>

            <footer class="dashboard-footer">
                <p>
                    © 2026 AdventureAja.
                    Temani setiap langkah petualanganmu.
                </p>
            </footer>

        </main>
    </div>

    <!-- Interaksi sidebar -->
    <script src="js/script.js"></script>

</body>
</html>
