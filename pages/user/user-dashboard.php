<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_login();

if (($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: admin.php');
    exit;
}

require_once APP_ROOT . '/config/koneksi.php';

$user = $_SESSION['user'];
$userId = (int)($user['id'] ?? 0);

if ($userId <= 0) {
    $email = $user['email'] ?? '';
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        exit('Gagal menyiapkan data pengguna.');
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $dbUser = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$dbUser) {
        exit('Akun pengguna tidak ditemukan. Silakan login kembali.');
    }

    $userId = (int)$dbUser['id'];
    $_SESSION['user']['id'] = $userId;
}

$userName = $user['name'] ?? 'Pengguna';
$userEmail = $user['email'] ?? '';

function rupiah($value): string {
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}

function statusLabel($status): string {
    $labels = [
        'pending' => 'Menunggu Konfirmasi',
        'active' => 'Sedang Diproses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? ucfirst((string)$status);
}

/*
 * Mencari file gambar berdasarkan nilai kolom products.image.
 * Mendukung nama file saja maupun path relatif seperti uploads/foto.jpg.
 */
function productImageUrl($image): string {
    $image = trim((string)$image);
    if ($image === '') {
        return '';
    }

    $image = str_replace('\\', '/', $image);

    // URL eksternal
    if (preg_match('~^https?://~i', $image)) {
        return $image;
    }

    // Hilangkan path absolut Windows/Linux jika tersimpan di database.
    $projectPath = str_replace('\\', '/', APP_ROOT);
    $projectPath = rtrim($projectPath, '/');
    if (stripos($image, $projectPath . '/') === 0) {
        $image = substr($image, strlen($projectPath) + 1);
    }

    $image = ltrim($image, '/');
    $baseName = basename($image);

    $legacyImageMap = [
        'tenda-3-layer.jpg' => 'assets/tenda.png',
        'tenda-3-layer.png' => 'assets/tenda.png',
        'carrier-60l.jpg' => 'assets/carrier.png',
        'carrier-60l.png' => 'assets/carrier.png',
        'sleeping-bag.jpg' => 'assets/sleeping-bag.png',
        'lampu-tenda.jpg' => 'assets/lampu.png',
        'lampu-tenda.png' => 'assets/lampu.png',
    ];

    $mappedImage = $legacyImageMap[strtolower($baseName)] ?? null;
    $candidates = [
        $image,
        'uploads/' . $baseName,
        'uploads/products/' . $baseName,
        'assets/uploads/' . $baseName,
        'assets/images/' . $baseName,
        'images/' . $baseName,
        'assets/' . $baseName,
        $mappedImage,
    ];

    foreach (array_unique($candidates) as $candidate) {
        if (!$candidate) {
            continue;
        }

        $candidate = str_replace(['../', '..\\'], '', $candidate);
        $diskPath = APP_ROOT . '/' . $candidate;

        if (is_file($diskPath)) {
            return $candidate;
        }
    }

    return '';
}

// Ambil produk
$products = [];
$productQuery = mysqli_query(
    $conn,
    "SELECT id, name, description, price_per_day, stock, image
     FROM products
     WHERE stock > 0 AND status = 'active'
     ORDER BY id DESC"
);

if (!$productQuery) {
    exit('Gagal mengambil data produk: ' . e(mysqli_error($conn)));
}

while ($product = mysqli_fetch_assoc($productQuery)) {
    $products[] = $product;
}

// Ambil transaksi milik user
$orders = [];
$orderSql = "
    SELECT
        t.id,
        t.transaction_code,
        t.rental_start_date,
        t.rental_end_date,
        t.total_amount,
        t.status,
        t.created_at,
        GROUP_CONCAT(
            CONCAT(p.name, ' (', td.quantity, ' barang)')
            ORDER BY p.name SEPARATOR ', '
        ) AS product_names
    FROM transactions t
    LEFT JOIN transaction_details td ON td.transaction_id = t.id
    LEFT JOIN products p ON p.id = td.product_id
    WHERE t.user_id = ?
    GROUP BY
        t.id, t.transaction_code, t.rental_start_date,
        t.rental_end_date, t.total_amount, t.status, t.created_at
    ORDER BY t.created_at DESC
";

$stmt = mysqli_prepare($conn, $orderSql);
if (!$stmt) {
    exit('Gagal menyiapkan data transaksi: ' . e(mysqli_error($conn)));
}
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);

while ($order = mysqli_fetch_assoc($orderResult)) {
    $orders[] = $order;
}
mysqli_stmt_close($stmt);

$totalOrders = count($orders);
$processingOrders = 0;
$completedOrders = 0;

foreach ($orders as $order) {
    if (in_array($order['status'], ['pending', 'active'], true)) {
        $processingOrders++;
    }
    if ($order['status'] === 'completed') {
        $completedOrders++;
    }
}
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
*{margin:0;padding:0;box-sizing:border-box;font-family:"Poppins",sans-serif}
:root{--yellow:#d6ad45;--navy:#101c2c;--panel:#172638;--muted:#aab6c5;--white:#f4f6fa;--green:#48d597;--border:rgba(255,255,255,.07)}
body{min-height:100vh;color:var(--white);background:linear-gradient(rgba(7,16,29,.60),rgba(7,16,29,.72)),url("assets/backgrounddashboard.jpg") center/cover fixed}
a{color:inherit;text-decoration:none}
.sidebar{position:fixed;inset:0 auto 0 0;width:245px;padding:27px 18px;display:flex;flex-direction:column;background:rgba(16,28,44,.98);border-right:1px solid var(--border);z-index:10;transition:transform .25s}
body.sidebar-collapsed .sidebar{transform:translateX(-100%)}
.sidebar-overlay{position:fixed;inset:0;z-index:9;background:rgba(3,10,18,.68);backdrop-filter:blur(3px)}
.sidebar-overlay[hidden]{display:none}
.logo{padding:0 10px;margin-bottom:38px;font-size:24px;font-weight:800}.logo span{color:var(--yellow)}
.nav-label{padding:0 12px;margin-bottom:13px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1.5px}
.nav-link{display:flex;align-items:center;gap:13px;padding:13px 14px;margin-bottom:7px;border-radius:10px;color:#c1cbd8;font-size:13px;transition:.2s}
.nav-link i{font-size:21px}.nav-link:hover,.nav-link.active{color:var(--yellow);background:rgba(214,173,69,.10)}
.sidebar-bottom{margin-top:auto}
.main{margin-left:245px;padding:28px 35px 40px;max-width:1600px;transition:margin-left .25s}
body.sidebar-collapsed .main{margin-left:0}
.topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:30px}
.topbar-start{display:flex;align-items:center;gap:12px}
.sidebar-toggle{width:42px;height:42px;display:grid;place-items:center;flex-shrink:0;border:1px solid var(--border);border-radius:10px;background:var(--panel);color:var(--white);font-size:23px;cursor:pointer;transition:background .2s,border-color .2s,color .2s}
.sidebar-toggle:hover{border-color:rgba(214,173,69,.45);background:rgba(214,173,69,.12);color:var(--yellow)}
.sidebar-toggle:focus-visible{outline:2px solid var(--yellow);outline-offset:2px}
.topbar h2{font-size:22px}.topbar p{color:var(--muted);font-size:12px;margin-top:4px}
.account{display:flex;align-items:center;gap:11px;padding:9px 14px;border:1px solid var(--border);background:var(--panel);border-radius:12px}
.avatar{width:39px;height:39px;display:grid;place-items:center;border-radius:50%;background:var(--yellow);color:var(--navy);font-size:21px}
.account strong{display:block;font-size:12px}.account small{display:block;color:var(--muted);font-size:10px}
.page-section[hidden]{display:none!important}
.hero{display:flex;min-height:320px;justify-content:space-between;align-items:center;gap:25px;padding:38px;margin-bottom:26px;border:1px solid var(--border);border-radius:19px;background:linear-gradient(100deg,rgba(10,24,38,.96) 0%,rgba(10,24,38,.84) 48%,rgba(10,24,38,.28) 100%),url("assets/backgrounddashboard.jpg") center 68%/cover;box-shadow:0 18px 45px rgba(0,0,0,.22)}
.eyebrow{color:var(--yellow);font-size:10px;font-weight:700;letter-spacing:1.6px}
.hero h1{margin:11px 0;font-size:clamp(24px,3vw,35px);line-height:1.3}
.hero p{max-width:520px;color:#c0ccda;font-size:13px;line-height:1.9}
.primary-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 17px;margin-top:20px;border:0;border-radius:9px;background:var(--yellow);color:#101c2c;font-size:12px;font-weight:700;cursor:pointer}
.primary-btn:hover{background:#e4bd61;transform:translateY(-2px)}
.hero-icon{flex-shrink:0;color:var(--yellow);font-size:100px;opacity:.75}
.section-title{margin-bottom:16px;font-size:18px}
.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px;margin-bottom:30px}
.stat-card{padding:22px;border:1px solid var(--border);border-radius:14px;background:rgba(23,38,56,.96)}
.stat-top{display:flex;align-items:center;justify-content:space-between;color:var(--muted);font-size:12px}
.stat-top i{color:var(--yellow);font-size:25px}.stat-number{margin-top:15px;font-size:29px;font-weight:700}.stat-desc{color:var(--muted);font-size:11px}
.content-card{margin-bottom:25px;overflow:hidden;border:1px solid var(--border);border-radius:15px;background:rgba(23,38,56,.96)}
.card-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:20px 23px;border-bottom:1px solid var(--border)}
.card-heading h3{font-size:15px}.card-heading a{color:var(--yellow);font-size:11px}
.order-item{display:flex;align-items:center;gap:15px;padding:19px 23px;border-bottom:1px solid rgba(255,255,255,.05)}
.order-item:last-child{border-bottom:0}
.order-icon{width:45px;height:45px;display:grid;place-items:center;flex-shrink:0;border-radius:11px;background:rgba(214,173,69,.10);color:var(--yellow);font-size:23px}
.order-info{flex:1;min-width:0}.order-info strong{font-size:12px}
.order-info p{margin-top:4px;color:var(--muted);font-size:10px;line-height:1.8}
.status{display:inline-block;padding:5px 9px;border-radius:20px;background:rgba(214,173,69,.10);color:var(--yellow);font-size:10px;white-space:nowrap}
.status.completed{color:var(--green);background:rgba(72,213,151,.10)}
.status.cancelled{color:#ff9999;background:rgba(255,100,100,.10)}
.empty-state{padding:32px 20px;text-align:center;color:var(--muted);font-size:12px}
.empty-state i{display:block;margin-bottom:10px;font-size:35px}
.catalog-page{padding:25px;margin-bottom:25px;border:1px solid var(--border);border-radius:16px;background:rgba(23,38,56,.96)}
.catalog-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:25px}
.catalog-heading h2{margin:9px 0;font-size:25px}.catalog-heading p{color:var(--muted);font-size:12px;line-height:1.8}
.catalog-count{color:var(--muted);font-size:11px}
.catalog-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px}
.catalog-product{display:flex;min-width:0;flex-direction:column;overflow:hidden;border:1px solid var(--border);border-radius:13px;background:#101c2c;transition:.2s}
.catalog-product:hover{transform:translateY(-4px);border-color:rgba(214,173,69,.35)}
.catalog-product img{display:block;width:100%;height:175px;object-fit:cover;background:#24384b}
.product-image-placeholder{height:175px;display:grid;place-items:center;background:#24384b;color:var(--yellow);font-size:55px}
.catalog-product-info{display:flex;flex:1;flex-direction:column;padding:16px}.catalog-product-info>span{color:var(--muted);font-size:10px}
.catalog-product-info h3{min-height:38px;margin:7px 0 12px;color:var(--white);font-size:14px;line-height:1.4}
.catalog-product-info p{color:#e0bd68;font-size:14px;font-weight:700}
.catalog-product-info small{color:var(--muted);font-size:10px;font-weight:400}
.catalog-stock{margin-top:8px!important;color:var(--muted)!important;font-size:11px!important;font-weight:400!important}
.primary-btn,.catalog-button{min-height:42px;border:0;border-radius:9px;background:var(--yellow);color:var(--navy);font-weight:700;transition:.2s}
.primary-btn:hover,.catalog-button:hover{background:#e4bd61;transform:translateY(-2px)}
.catalog-button{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;margin-top:auto;padding:10px;text-align:center;font-size:11px}
.catalog-button i{font-size:17px}
.stock-empty{color:#ff9999;font-size:11px}
.profile-description{padding:18px 23px;color:var(--muted);font-size:11px;line-height:1.8}
.footer{padding:18px 0;text-align:center;color:var(--muted);font-size:11px}
@media(max-width:1100px){.catalog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:900px){.sidebar{width:205px}.main{margin-left:205px;padding:24px 20px}.stats{grid-template-columns:1fr}}
@media(max-width:650px){.sidebar{width:245px;transform:translateX(-100%)}body.menu-open .sidebar{transform:translateX(0)}.main{margin-left:0;padding:20px 15px}.hero{min-height:300px;padding:26px 22px;background-image:linear-gradient(100deg,rgba(10,24,38,.88),rgba(10,24,38,.66)),url("assets/backgrounddashboard.jpg");background-position:center,62% center}.hero-icon{display:none}.stats{grid-template-columns:1fr}.order-item{flex-wrap:wrap;padding:16px}.order-info{flex-basis:calc(100% - 65px)}.status{margin-left:60px}.catalog-page{padding:18px}.catalog-grid{grid-template-columns:1fr}.catalog-product img,.product-image-placeholder{height:210px}}
</style>
</head>
<body>
<aside class="sidebar" id="userSidebar">
    <a href="#dashboard" class="logo" data-page="dashboard">Adventure<span>Aja</span></a>
    <p class="nav-label">MENU PENGGUNA</p>
    <a href="#dashboard" class="nav-link active" data-page="dashboard"><i class="bx bx-grid-alt"></i><span>Dashboard</span></a>
    <a href="#katalog" class="nav-link" data-page="katalog"><i class="bx bx-backpack"></i><span>Katalog Camping</span></a>
    <a href="#pesanan" class="nav-link" data-page="pesanan"><i class="bx bx-receipt"></i><span>Pesanan Saya</span></a>
    <a href="#profil" class="nav-link" data-page="profil"><i class="bx bx-user"></i><span>Profil Saya</span></a>
    <div class="sidebar-bottom"><a href="logout.php" class="nav-link"><i class="bx bx-log-out"></i><span>Keluar</span></a></div>
</aside>
<div class="sidebar-overlay" id="userSidebarOverlay" hidden></div>

<main class="main">
<header class="topbar">
    <div class="topbar-start">
        <button type="button" class="sidebar-toggle" id="userSidebarToggle" aria-label="Tutup sidebar" aria-controls="userSidebar" aria-expanded="true">
            <i class="bx bx-menu" aria-hidden="true"></i>
        </button>
        <div><h2 id="pageTitle">Dashboard Saya</h2><p id="currentDate"></p></div>
    </div>
    <div class="account">
        <div class="avatar"><i class="bx bx-user"></i></div>
        <div><strong><?= e($userName) ?></strong><small>Member AdventureAja</small></div>
    </div>
</header>

<div class="page-section" data-page-section="dashboard" id="dashboard">
    <section class="hero">
        <div>
            <span class="eyebrow">YOUR NEXT ADVENTURE</span>
            <h1>Hai, <?= e(explode(' ', $userName)[0]) ?>!</h1>
            <p>Selamat datang di dashboard AdventureAja. Siapkan perlengkapan terbaik dan mulai rencanakan petualanganmu bersama kami.</p>
            <a href="#katalog" class="primary-btn" data-page="katalog">Jelajahi Katalog <i class="bx bx-right-arrow-alt"></i></a>
        </div>
        <i class="bx bx-mountain hero-icon"></i>
    </section>

    <section>
        <h2 class="section-title">Ringkasan Pesanan</h2>
        <div class="stats">
            <article class="stat-card"><div class="stat-top">Total Pesanan <i class="bx bx-package"></i></div><div class="stat-number"><?= $totalOrders ?></div><p class="stat-desc">Jumlah pesanan akun ini</p></article>
            <article class="stat-card"><div class="stat-top">Sedang Diproses <i class="bx bx-time-five"></i></div><div class="stat-number"><?= $processingOrders ?></div><p class="stat-desc">Pesanan yang masih berjalan</p></article>
            <article class="stat-card"><div class="stat-top">Selesai <i class="bx bx-check-circle"></i></div><div class="stat-number"><?= $completedOrders ?></div><p class="stat-desc">Pesanan yang sudah selesai</p></article>
        </div>
    </section>

    <section class="content-card">
        <div class="card-heading"><h3>Pesanan Terbaru</h3><a href="#pesanan" data-page="pesanan">Lihat Semua</a></div>
        <?php if (empty($orders)): ?>
            <div class="empty-state"><i class="bx bx-receipt"></i>Belum ada pesanan pada akun ini.<br>Yuk, mulai pilih perlengkapan camping!</div>
        <?php else: ?>
            <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                <div class="order-item">
                    <div class="order-icon"><i class="bx bx-backpack"></i></div>
                    <div class="order-info">
                        <strong><?= e($order['transaction_code']) ?></strong>
                        <p><?= e($order['product_names'] ?: 'Detail produk tidak tersedia') ?></p>
                        <p><?= e($order['rental_start_date']) ?> sampai <?= e($order['rental_end_date']) ?></p>
                        <p><?= rupiah($order['total_amount']) ?></p>
                    </div>
                    <span class="status <?= e($order['status']) ?>"><?= e(statusLabel($order['status'])) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>

<section class="page-section catalog-page" data-page-section="katalog" id="katalog" hidden>
    <div class="catalog-heading">
        <div><span class="eyebrow">PERLENGKAPAN PILIHAN</span><h2>Siapkan Petualanganmu</h2><p>Temukan perlengkapan yang kamu butuhkan untuk perjalanan yang lebih nyaman.</p></div>
        <span class="catalog-count"><?= count($products) ?> produk tersedia</span>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state"><i class="bx bx-backpack"></i>Belum ada produk yang tersedia saat ini.</div>
    <?php else: ?>
        <div class="catalog-grid">
            <?php foreach ($products as $product): ?>
                    <?php $imageSrc = productImageUrl($product['image'] ?? ''); ?>
                <article class="catalog-product">
                    <?php if ($imageSrc !== ''): ?>
                        <img src="<?= e($imageSrc) ?>" alt="<?= e($product['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                        <div class="product-image-placeholder" style="display:none"><i class="bx bx-image"></i></div>
                    <?php else: ?>
                        <div class="product-image-placeholder"><i class="bx bx-image"></i></div>
                    <?php endif; ?>

                    <div class="catalog-product-info">
                        <span>Perlengkapan Camping</span>
                        <h3><?= e($product['name']) ?></h3>
                        <p><?= rupiah($product['price_per_day']) ?> <small>/ hari</small></p>
                        <p class="catalog-stock">Stok: <?= (int)$product['stock'] ?></p>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <a href="checkout.php?product_id=<?= (int)$product['id'] ?>" class="catalog-button"><i class="bx bx-cart"></i> Sewa Sekarang</a>
                        <?php else: ?>
                            <span class="stock-empty">Stok tidak tersedia</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="page-section content-card" data-page-section="pesanan" id="pesanan" hidden>
    <div class="card-heading"><h3>Pesanan Saya</h3><a href="#katalog" data-page="katalog">Lihat Katalog</a></div>
    <?php if (empty($orders)): ?>
        <div class="empty-state"><i class="bx bx-receipt"></i>Belum ada pesanan pada akun ini.<br>Yuk, mulai pilih perlengkapan camping!<br><a href="#katalog" class="primary-btn" data-page="katalog">Mulai Sewa</a></div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-item">
                <div class="order-icon"><i class="bx bx-package"></i></div>
                <div class="order-info">
                    <strong><?= e($order['transaction_code']) ?></strong>
                    <p>Barang: <?= e($order['product_names'] ?: 'Detail produk tidak tersedia') ?></p>
                    <p>Tanggal sewa: <?= e($order['rental_start_date']) ?> - <?= e($order['rental_end_date']) ?></p>
                    <p>Total: <?= rupiah($order['total_amount']) ?></p>
                    <p>Dibuat: <?= e($order['created_at']) ?></p>
                </div>
                <span class="status <?= e($order['status']) ?>"><?= e(statusLabel($order['status'])) ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section class="page-section content-card" data-page-section="profil" id="profil" hidden>
    <div class="card-heading"><h3>Informasi Akun</h3></div>
    <p class="profile-description">Berikut adalah informasi akun yang sedang kamu gunakan untuk mengakses AdventureAja.</p>
    <div class="order-item"><div class="order-icon"><i class="bx bx-user"></i></div><div class="order-info"><strong>Nama Lengkap</strong><p><?= e($userName) ?></p></div></div>
    <div class="order-item"><div class="order-icon"><i class="bx bx-envelope"></i></div><div class="order-info"><strong>Email</strong><p><?= e($userEmail) ?></p></div></div>
</section>

<footer class="footer">Ã‚Â© AdventureAja 2026 Ã¢â‚¬â€ Temani setiap langkah petualanganmu.</footer>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("currentDate").textContent = new Date().toLocaleDateString("id-ID", {
        weekday:"long", day:"numeric", month:"long", year:"numeric"
    });

    const pageTitles = {
        dashboard:"Dashboard Saya",
        katalog:"Katalog Camping",
        pesanan:"Pesanan Saya",
        profil:"Profil Saya"
    };

    const sidebarToggle = document.getElementById("userSidebarToggle");
    const sidebarOverlay = document.getElementById("userSidebarOverlay");
    const sidebarToggleIcon = sidebarToggle.querySelector("i");
    const isMobileScreen = () => window.matchMedia("(max-width: 650px)").matches;

    function closeMobileSidebar() {
        document.body.classList.remove("menu-open");
        sidebarOverlay.hidden = true;
        sidebarToggle.setAttribute("aria-expanded", "false");
        sidebarToggle.setAttribute("aria-label", "Buka sidebar");
        sidebarToggleIcon.className = "bx bx-menu";
    }

    if (isMobileScreen()) closeMobileSidebar();

    sidebarToggle.addEventListener("click", function() {
        if (isMobileScreen()) {
            const isOpen = document.body.classList.toggle("menu-open");
            sidebarOverlay.hidden = !isOpen;
            sidebarToggle.setAttribute("aria-expanded", String(isOpen));
            sidebarToggle.setAttribute("aria-label", isOpen ? "Tutup sidebar" : "Buka sidebar");
            sidebarToggleIcon.className = isOpen ? "bx bx-x" : "bx bx-menu";
            return;
        }

        const isCollapsed = document.body.classList.toggle("sidebar-collapsed");
        sidebarToggle.setAttribute("aria-expanded", String(!isCollapsed));
        sidebarToggle.setAttribute("aria-label", isCollapsed ? "Buka sidebar" : "Tutup sidebar");
        sidebarToggleIcon.className = isCollapsed ? "bx bx-menu" : "bx bx-menu-alt-left";
    });

    sidebarOverlay.addEventListener("click", closeMobileSidebar);

    window.addEventListener("resize", function() {
        document.body.classList.remove("menu-open", "sidebar-collapsed");
        sidebarOverlay.hidden = true;
        const isMobile = isMobileScreen();
        sidebarToggle.setAttribute("aria-expanded", String(!isMobile));
        sidebarToggle.setAttribute("aria-label", isMobile ? "Buka sidebar" : "Tutup sidebar");
        sidebarToggleIcon.className = isMobile ? "bx bx-menu" : "bx bx-menu-alt-left";
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape" && isMobileScreen() && document.body.classList.contains("menu-open")) {
            closeMobileSidebar();
        }
    });

    function showPage(page) {
        if (!pageTitles[page]) page = "dashboard";

        document.querySelectorAll("[data-page-section]").forEach(function(section) {
            section.hidden = section.dataset.pageSection !== page;
        });

        document.querySelectorAll(".nav-link[data-page]").forEach(function(link) {
            link.classList.toggle("active", link.dataset.page === page);
        });

        document.getElementById("pageTitle").textContent = pageTitles[page];
        if (isMobileScreen()) closeMobileSidebar();

        if (window.location.hash !== "#" + page) {
            history.replaceState(null, "", "#" + page);
        }

        window.scrollTo({top:0, behavior:"smooth"});
    }

    document.querySelectorAll("[data-page]").forEach(function(link) {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            showPage(this.dataset.page);
        });
    });

    window.addEventListener("hashchange", function() {
        showPage(window.location.hash.replace("#", "") || "dashboard");
    });

    showPage(window.location.hash.replace("#", "") || "dashboard");
});
</script>
</body>
</html>
