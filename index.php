
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AdventureAja | Temani Petualanganmu</title>

    <link rel="stylesheet" href="css/user.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
  </head>

  <body>
    
<!-- SIDEBAR USER -->
<aside class="user-sidebar" id="userSidebar">
  <div class="user-sidebar-header">
    <a href="index.php" class="user-logo">
      Adventure<span>Aja</span>
    </a>

    <button
      type="button"
      class="user-sidebar-toggle"
      id="userSidebarToggle"
      aria-label="Ciutkan sidebar"
    >
      <i class="bx bx-menu"></i>
    </button>
  </div>

  <nav class="user-sidebar-nav">
    <a href="index.php" class="user-nav-link active" title="Home">
      <i class="bx bx-home-alt"></i>
      <span>Home</span>
    </a>

    <a href="#katalog" class="user-nav-link" title="Katalog Camping">
      <i class="bx bx-backpack"></i>
      <span>Katalog Camping</span>
    </a>

    <a href="login.php" class="user-nav-link" title="Pesanan Saya">
      <i class="bx bx-receipt"></i>
      <span>Pesanan Saya</span>
    </a>

    <a href="login.php" class="user-nav-link" title="Profil Saya">
      <i class="bx bx-user"></i>
      <span>Profil Saya</span>
    </a>

    <a href="#tentang" class="user-nav-link" title="Tentang Kami">
      <i class="bx bx-info-circle"></i>
      <span>Tentang Kami</span>
    </a>
  </nav>

  <div class="user-sidebar-bottom">
    <a href="login.php" class="user-nav-link" title="Masuk">
      <i class="bx bx-log-in"></i>
      <span>Masuk</span>
    </a>

    <a href="register.php" class="user-register-link">
      <i class="bx bx-user-plus"></i>
      <span>Daftar Akun</span>
    </a>
  </div>
</aside>

<!-- OVERLAY UNTUK TAMPILAN HP -->
<div class="user-sidebar-overlay" id="userSidebarOverlay"></div>

<!-- HEADER KONTEN USER -->
<header class="user-topbar">
  <button
    type="button"
    class="user-mobile-toggle"
    id="userMobileToggle"
    aria-label="Buka sidebar"
  >
    <i class="bx bx-menu"></i>
  </button>

  <div class="user-topbar-title">
    <span>AdventureAja</span>
  </div>

  <div class="user-topbar-date">
  <span id="userDate">Memuat tanggal...</span>
  <span id="userClock">00:00:00</span>
</div>

  <a href="login.php" class="user-topbar-account">
    <i class="bx bx-user-circle"></i>
    <span>Masuk</span>
  </a>
</header>


   <div class="user-page-content">
    <main>
      <!-- HERO -->
      <section class="hero">
        <div class="hero-content">
          <span class="hero-label">
            <i class="bx bx-compass"></i>
            EXPLORE THE OUTDOORS
          </span>

          <h1 id="userHeroTitle">
            Malang Mbois Ilakes Bareng AdventureAja.
         </h1>

          <p>
            Mulai petualanganmu tanpa ribet bersama AdventureAja.
            Sewa perlengkapan camping berkualitas untuk menemani
            perjalananmu menjelajahi alam.
          </p>

          <div class="hero-buttons">
            <a href="#katalog" class="btn-primary" id="userCatalogButton">
                Jelajahi Katalog
                <i class="bx bx-right-arrow-alt"></i>
            </a>

            <a href="#tentang" class="btn-outline">
              Kenali Kami
            </a>
          </div>

          <div class="hero-features">
            <div class="feature-item">
              <i class="bx bx-check-circle"></i>
              <span>Praktis dan Mudah</span>
            </div>

            <div class="feature-item">
              <i class="bx bx-check-circle"></i>
              <span>Peralatan Berkualitas</span>
            </div>
          </div>
        </div>

        <div class="hero-image">
          <img
            src="assets/backgrounddashboard.jpg"
            alt="Pemandangan alam untuk petualangan camping"
          />

          <div class="image-badge">
            <i class="bx bx-leaf"></i>
            <div>
              <strong>Start Your Adventure</strong>
              <span>Alam menunggu untuk dijelajahi</span>
            </div>
          </div>
        </div>
      </section>

      
<section id="katalog">
  <h2>Katalog Camping</h2>

  <!-- TAMBAHKAN KODE FILTER DI SINI -->
  <div class="catalog-filter">
    <label for="kategoriCamping">Pilih kategori:</label>
    <select id="kategoriCamping">
      <option value="">Semua Perlengkapan</option>
      <option value="tenda">Tenda</option>
      <option value="carrier">Carrier</option>
      <option value="tidur">Perlengkapan Tidur</option>
      <option value="lampu">Lampu Camping</option>
    </select>
  </div>

  <!-- Daftar produk yang sudah ada, jangan dihapus -->
  <div class="catalog-grid">
    <!-- Kartu Tenda, Carrier, Sleeping Bag, Lampu Tenda -->
  </div>
</section>


      <!-- KATALOG -->
      <section class="catalog-section" id="katalog">
        <div class="section-heading">
          <div>
            <span class="section-label">PERLENGKAPAN PILIHAN</span>
            <h2>Siapkan Petualanganmu</h2>
            <p>
              Temukan perlengkapan yang kamu butuhkan untuk perjalanan
              yang lebih nyaman.
            </p>
          </div>

          <a href="login.php" class="catalog-link">
            Lihat Selengkapnya
            <i class="bx bx-right-arrow-alt"></i>
          </a>
        </div>

        <div class="catalog-grid">
          <article class="product-card">
            <div class="product-image">
              <img src="assets/tenda.png" alt="Tenda camping 3 layer" />
              <span class="product-tag">TERLARIS</span>
            </div>

            <div class="product-info">
              <span class="product-category">Tempat Berteduh</span>
              <h3>Tenda 3 Layer</h3>
              <div class="product-rating">
                <i class="bx bxs-star"></i>
                <span>4.8</span>
                <span class="rating-count">(50+)</span>
              </div>
              <div class="product-bottom">
                <p><strong>Rp40.000</strong><span> / hari</span></p>
                <a href="login.php" class="product-button" aria-label="Sewa Tenda 3 Layer">
                  <i class="bx bx-plus"></i>
                </a>
              </div>
            </div>
          </article>

          <article class="product-card">
            <div class="product-image">
              <img src="assets/carrier.png" alt="Tas carrier 60 liter" />
            </div>

            <div class="product-info">
              <span class="product-category">Tas Pendakian</span>
              <h3>Carrier 60L</h3>
              <div class="product-rating">
                <i class="bx bxs-star"></i>
                <span>4.6</span>
                <span class="rating-count">(60+)</span>
              </div>
              <div class="product-bottom">
                <p><strong>Rp50.000</strong><span> / hari</span></p>
                <a href="login.php" class="product-button" aria-label="Sewa Carrier 60L">
                  <i class="bx bx-plus"></i>
                </a>
              </div>
            </div>
          </article>

          <article class="product-card">
            <div class="product-image">
              <img src="assets/sleeping-bag.png" alt="Sleeping bag camping" />
            </div>

            <div class="product-info">
              <span class="product-category">Perlengkapan Tidur</span>
              <h3>Sleeping Bag</h3>
              <div class="product-rating">
                <i class="bx bxs-star"></i>
                <span>4.5</span>
                <span class="rating-count">(80+)</span>
              </div>
              <div class="product-bottom">
                <p><strong>Rp25.000</strong><span> / hari</span></p>
                <a href="login.php" class="product-button" aria-label="Sewa Sleeping Bag">
                  <i class="bx bx-plus"></i>
                </a>
              </div>
            </div>
          </article>

          <article class="product-card">
            <div class="product-image">
              <img src="assets/lampu.png" alt="Lampu tenda camping" />
            </div>

            <div class="product-info">
              <span class="product-category">Penerangan</span>
              <h3>Lampu Tenda</h3>
              <div class="product-rating">
                <i class="bx bxs-star"></i>
                <span>4.7</span>
                <span class="rating-count">(40+)</span>
              </div>
              <div class="product-bottom">
                <p><strong>Rp7.000</strong><span> / hari</span></p>
                <a href="login.php" class="product-button" aria-label="Sewa Lampu Tenda">
                  <i class="bx bx-plus"></i>
                </a>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- TENTANG KAMI -->
      <section class="about-section" id="tentang">
        <div class="about-icon">
          <i class="bx bx-mountain"></i>
        </div>

        <div class="about-content">
          <span class="section-label">TENTANG ADVENTUREAJA</span>
          <h2>Petualangan Hebat Tidak Harus Mahal.</h2>
          <p>
            AdventureAja hadir untuk memudahkan kamu menikmati alam
            tanpa harus membeli semua perlengkapan camping sendiri.
            Pilih alat yang dibutuhkan, tentukan jadwal, dan bersiaplah
            untuk petualangan berikutnya.
          </p>
          <a href="register.php" class="btn-primary">
            Mulai Sekarang
            <i class="bx bx-right-arrow-alt"></i>
          </a>
        </div>
      </section>
    </main>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
      <a href="index.php" class="logo">
        Adventure<span>Aja</span>
      </a>

      <p>Temani setiap langkah petualanganmu.</p>
      <span class="copyright">© AdventureAja 2026</span>
    </footer>

    <!-- TOAST / SNACKBAR USER -->
    <div id="userSnackbar" class="user-snackbar" role="status" aria-live="polite">
        <i class="bx bx-check-circle"></i>
        <span id="userSnackbarMessage">Berhasil!</span>
    </div>
    
    <!-- Inspirasi Petualangan: fitur Fetch API dan Promise -->
<section class="inspiration-section">
    <h2>Inspirasi Hari Ini</h2>
    <p>Dapatkan kutipan untuk menemani petualanganmu.</p>

    <div id="quote-result" aria-live="polite">
        Klik tombol untuk memuat inspirasi.
    </div>

    <button type="button" id="load-quote-btn">
        Muat Inspirasi
    </button>
</section>

<script src="js/user.js"></script>
</body>

  
  
  
</html>
