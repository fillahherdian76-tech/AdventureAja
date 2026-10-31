<?php
session_start();

// Proteksi dashboard: wajib login.
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$userName = htmlspecialchars(
    $user['name'] ?? 'Pengguna',
    ENT_QUOTES,
    'UTF-8'
);

$userEmail = htmlspecialchars(
    $user['email'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User | AdventureAja</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    :root {
      --yellow: #ffc107;
      --navy: #101c2c;
      --panel: #172638;
      --muted: #aab6c5;
      --white: #fff;
      --green: #48d597;
    }

    body {
      min-height: 100vh;
      color: var(--white);
      background:
        linear-gradient(rgba(7, 16, 29, .93), rgba(7, 16, 29, .97)),
        url("assets/backgrounddashboard.jpg") center/cover fixed;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    .sidebar {
      position: fixed;
      inset: 0 auto 0 0;
      width: 245px;
      padding: 27px 18px;
      display: flex;
      flex-direction: column;
      background: rgba(16, 28, 44, .98);
      border-right: 1px solid rgba(255,255,255,.08);
      z-index: 10;
    }

    .logo {
      padding: 0 10px;
      margin-bottom: 38px;
      font-size: 24px;
      font-weight: 800;
    }

    .logo span {
      color: var(--yellow);
    }

    .nav-label {
      padding: 0 12px;
      margin-bottom: 13px;
      color: var(--muted);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.5px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 13px;
      padding: 13px 14px;
      margin-bottom: 7px;
      border-radius: 10px;
      color: #c1cbd8;
      font-size: 13px;
      transition: .2s;
    }

    .nav-link i {
      font-size: 21px;
    }

    .nav-link:hover,
    .nav-link.active {
      color: var(--yellow);
      background: rgba(255,193,7,.12);
    }

    .sidebar-bottom {
      margin-top: auto;
    }

    .main {
      margin-left: 245px;
      padding: 28px 35px 40px;
      max-width: 1600px;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .topbar h2 {
      font-size: 22px;
    }

    .topbar p {
      color: var(--muted);
      font-size: 12px;
      margin-top: 4px;
    }

    .account {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 9px 14px;
      border: 1px solid rgba(255,255,255,.1);
      background: var(--panel);
      border-radius: 12px;
    }

    .avatar {
      width: 39px;
      height: 39px;
      display: grid;
      place-items: center;
      border-radius: 50%;
      background: var(--yellow);
      color: var(--navy);
      font-size: 21px;
    }

    .account strong {
      display: block;
      font-size: 12px;
    }

    .account small {
      display: block;
      color: var(--muted);
      font-size: 10px;
    }

    .hero {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 25px;
      padding: 34px;
      margin-bottom: 26px;
      border: 1px solid rgba(255,255,255,.09);
      border-radius: 19px;
      background: linear-gradient(115deg, #24384b, #152435);
    }

    .eyebrow {
      color: var(--yellow);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.6px;
    }

    .hero h1 {
      margin: 11px 0;
      font-size: clamp(24px, 3vw, 35px);
      line-height: 1.3;
    }

    .hero p {
      max-width: 520px;
      color: #c0ccda;
      font-size: 13px;
      line-height: 1.9;
    }

    .primary-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 17px;
      margin-top: 20px;
      border: 0;
      border-radius: 9px;
      background: var(--yellow);
      color: var(--navy);
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
    }

    .hero-icon {
      flex-shrink: 0;
      color: var(--yellow);
      font-size: 100px;
      opacity: .9;
    }

    .section-title {
      margin-bottom: 16px;
      font-size: 18px;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 17px;
      margin-bottom: 30px;
    }

    .stat-card {
      padding: 22px;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 14px;
      background: rgba(23,38,56,.93);
    }

    .stat-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: var(--muted);
      font-size: 12px;
    }

    .stat-top i {
      color: var(--yellow);
      font-size: 25px;
    }

    .stat-number {
      margin-top: 15px;
      font-size: 29px;
      font-weight: 700;
    }

    .stat-desc {
      color: var(--muted);
      font-size: 11px;
    }

    .content-card {
      margin-bottom: 25px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 15px;
      background: rgba(23,38,56,.94);
    }

    .card-heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 20px 23px;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .card-heading h3 {
      font-size: 15px;
    }

    .card-heading a {
      color: var(--yellow);
      font-size: 11px;
    }

    .order-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 19px 23px;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }

    .order-item:last-child {
      border-bottom: 0;
    }

    .order-icon {
      width: 45px;
      height: 45px;
      display: grid;
      place-items: center;
      flex-shrink: 0;
      border-radius: 11px;
      background: rgba(255,193,7,.12);
      color: var(--yellow);
      font-size: 23px;
    }

    .order-info {
      flex: 1;
      min-width: 0;
    }

    .order-info strong {
      font-size: 12px;
    }

    .order-info p {
      margin-top: 4px;
      color: var(--muted);
      font-size: 10px;
    }

    .status {
      display: inline-block;
      padding: 5px 9px;
      border-radius: 20px;
      background: rgba(255,193,7,.12);
      color: var(--yellow);
      font-size: 10px;
      white-space: nowrap;
    }

    .status.complete {
      color: var(--green);
      background: rgba(72,213,151,.12);
    }

    .empty-state {
      padding: 32px 20px;
      text-align: center;
      color: var(--muted);
      font-size: 12px;
    }

    .empty-state i {
      display: block;
      margin-bottom: 10px;
      font-size: 35px;
    }

    .footer {
      padding: 18px 0;
      text-align: center;
      color: var(--muted);
      font-size: 11px;
    }

    .toast {
      position: fixed;
      right: 24px;
      bottom: 24px;
      z-index: 30;
      max-width: calc(100vw - 40px);
      padding: 14px 19px;
      border-radius: 10px;
      background: var(--yellow);
      color: var(--navy);
      font-size: 12px;
      font-weight: 600;
      box-shadow: 0 8px 30px rgba(0,0,0,.25);
      opacity: 0;
      transform: translateY(15px);
      pointer-events: none;
      transition: .25s;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    @media (max-width: 900px) {
      .sidebar {
        width: 205px;
      }

      .main {
        margin-left: 205px;
        padding: 24px 20px;
      }

      .stats {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 650px) {
      .sidebar {
        width: 245px;
        transform: translateX(-100%);
        transition: transform .25s;
      }

      body.menu-open .sidebar {
        transform: translateX(0);
      }

      .main {
        margin-left: 0;
        padding: 20px 15px;
      }

      .topbar {
        align-items: flex-start;
      }

      .hero {
        padding: 25px 21px;
      }

      .hero-icon {
        display: none;
      }

      .stats {
        grid-template-columns: 1fr;
      }

      .order-item {
        flex-wrap: wrap;
        padding: 16px;
      }

      .order-info {
        flex-basis: calc(100% - 65px);
      }

      .status {
        margin-left: 60px;
      }
    }
  </style>
</head>

<body>
  
<body>

  <script>
  const currentUser = <?= json_encode([
      'name' => $user['name'] ?? 'Pengguna',
      'email' => $user['email'] ?? ''
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>


  <!-- Sidebar khusus pengguna -->
  <aside class="sidebar">
    <a href="index.php" class="logo">
      Adventure<span>Aja</span>
    </a>

    <p class="nav-label">MENU PENGGUNA</p>

    <a href="#dashboard" class="nav-link active">
      <i class="bx bx-grid-alt"></i> Dashboard
    </a>

    <a href="index.php#katalog" class="nav-link">
      <i class="bx bx-backpack"></i> Katalog Camping
    </a>

    <a href="#pesanan" class="nav-link">
      <i class="bx bx-receipt"></i> Pesanan Saya
    </a>

    <a href="#profil" class="nav-link">
      <i class="bx bx-user"></i> Profil Saya
    </a>

    <div class="sidebar-bottom">
      <a href="index.php" class="nav-link">
        <i class="bx bx-home"></i> Beranda
      </a>

      <a href="logout.php" class="nav-link" id="logoutButton">
          <i class="bx bx-log-out"></i> Keluar
      </a>
    </div>
  </aside>

  <main class="main" id="dashboard">
    <header class="topbar">
      <div>
        <h2>Dashboard Saya</h2>
        <p id="currentDate">Memuat tanggal...</p>
      </div>

      <div class="account">
        <div class="avatar"><i class="bx bx-user"></i></div>
        <div>
          <strong id="userName">Pengguna</strong>
          <small>Member AdventureAja</small>
        </div>
      </div>
    </header>

    <section class="hero">
      <div>
        <span class="eyebrow">YOUR NEXT ADVENTURE</span>
        <h1>Hai, <span id="greetingName">Petualang</span>!</h1>
        <p>
          Selamat datang di dashboard AdventureAja.
          Siapkan perlengkapan terbaik dan mulai rencanakan
          petualanganmu bersama kami.
        </p>
        <a href="index.php#katalog" class="primary-btn">
          Jelajahi Katalog <i class="bx bx-right-arrow-alt"></i>
        </a>
      </div>

      <i class="bx bx-mountain hero-icon"></i>
    </section>

    <section>
      <h2 class="section-title">Ringkasan Pesanan</h2>

      <div class="stats">
        <article class="stat-card">
          <div class="stat-top">
            Total Pesanan
            <i class="bx bx-package"></i>
          </div>
          <div class="stat-number" id="totalOrders">0</div>
          <p class="stat-desc">Jumlah pesanan akun ini</p>
        </article>

        <article class="stat-card">
          <div class="stat-top">
            Sedang Diproses
            <i class="bx bx-time-five"></i>
          </div>
          <div class="stat-number" id="processingOrders">0</div>
          <p class="stat-desc">Pesanan yang masih berjalan</p>
        </article>

        <article class="stat-card">
          <div class="stat-top">
            Selesai
            <i class="bx bx-check-circle"></i>
          </div>
          <div class="stat-number" id="completedOrders">0</div>
          <p class="stat-desc">Pesanan yang sudah selesai</p>
        </article>
      </div>
    </section>

    <section class="content-card" id="pesanan">
      <div class="card-heading">
        <h3>Pesanan Saya</h3>
        <a href="index.php#katalog">Sewa Perlengkapan</a>
      </div>

      <div id="orderList">
        <div class="empty-state">
          <i class="bx bx-receipt"></i>
          Belum ada pesanan pada akun ini.
          <br>Yuk, mulai pilih perlengkapan camping!
        </div>
      </div>
    </section>

    <section class="content-card" id="profil">
      <div class="card-heading">
        <h3>Informasi Akun</h3>
      </div>

      <div class="order-item">
        <div class="order-icon"><i class="bx bx-user"></i></div>
        <div class="order-info">
          <strong>Nama Lengkap</strong>
          <p id="profileName">-</p>
        </div>
      </div>

      <div class="order-item">
        <div class="order-icon"><i class="bx bx-envelope"></i></div>
        <div class="order-info">
          <strong>Email</strong>
          <p id="profileEmail">-</p>
        </div>
      </div>
    </section>

    <footer class="footer">
      © AdventureAja 2026 — Temani setiap langkah petualanganmu.
    </footer>
  </main>

  <div class="toast" id="userToast" role="status" aria-live="polite"></div>

  
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Identitas user berasal dari PHP Session.
    document.getElementById("userName").textContent = currentUser.name;
    document.getElementById("greetingName").textContent =
        currentUser.name.split(" ")[0];

    document.getElementById("profileName").textContent = currentUser.name;
    document.getElementById("profileEmail").textContent = currentUser.email;

    // Tampilkan tanggal hari ini.
    document.getElementById("currentDate").textContent =
        new Date().toLocaleDateString("id-ID", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric"
        });

    // Ambil pesanan milik user yang sedang login.
    let orders = [];

    try {
        const savedOrders = JSON.parse(
            localStorage.getItem("adventureAjaOrders") || "[]"
        );

        if (Array.isArray(savedOrders)) {
            orders = savedOrders.filter(function (order) {
                return String(order.userEmail || "").toLowerCase() ===
                    currentUser.email.toLowerCase();
            });
        }
    } catch (error) {
        orders = [];
    }

    const completed = orders.filter(function (order) {
        return String(order.status || "").toLowerCase() === "selesai";
    }).length;

    const processing = orders.filter(function (order) {
        const status = String(order.status || "").toLowerCase();

        return ["diproses", "sedang disewa"].includes(status);
    }).length;

    document.getElementById("totalOrders").textContent = orders.length;
    document.getElementById("processingOrders").textContent = processing;
    document.getElementById("completedOrders").textContent = completed;

    // Render daftar pesanan.
    const orderList = document.getElementById("orderList");

    if (orders.length > 0) {
        orderList.replaceChildren();

        orders.forEach(function (order) {
            const item = document.createElement("div");
            item.className = "order-item";

            const icon = document.createElement("div");
            icon.className = "order-icon";

            const iconElement = document.createElement("i");
            iconElement.className = "bx bx-backpack";
            icon.appendChild(iconElement);

            const info = document.createElement("div");
            info.className = "order-info";

            const product = document.createElement("strong");
            product.textContent =
                order.productName || order.product || "Perlengkapan camping";

            const details = document.createElement("p");
            details.textContent = order.date || "Tanggal belum ditentukan";

            info.append(product, details);

            const status = document.createElement("span");
            const statusText = String(order.status || "Diproses");

            status.className = "status";
            status.textContent = statusText;

            if (statusText.toLowerCase() === "selesai") {
                status.classList.add("complete");
            }

            item.append(icon, info, status);
            orderList.appendChild(item);
        });
    }

    // Logout lewat PHP agar session server benar-benar dihapus.
    document.getElementById("logoutButton").addEventListener(
        "click",
        function (event) {
            event.preventDefault();
            window.location.href = "logout.php";
        }
    );
});
</script>

</body>
</html>
