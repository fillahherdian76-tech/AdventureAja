<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_login();

if (($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: admin.php');
    exit;
}

require_once APP_ROOT . '/config/koneksi.php';

$userSession = $_SESSION['user'];
$userId = (int)($userSession['id'] ?? 0);
$selectedProductId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT) ?: 0;

// Jika session belum memiliki ID, cari berdasarkan email.
if ($userId <= 0) {
    $email = $userSession['email'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $dbUser = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$dbUser) {
        exit('Data akun tidak ditemukan. Silakan login kembali.');
    }

    $userId = (int)$dbUser['id'];
}

// Ambil data produk dari database.
$productResult = mysqli_query(
    $conn,
    "SELECT id, name, description, price_per_day, stock, image
     FROM products
     WHERE stock > 0 AND status = 'active'
     ORDER BY name ASC"
);

if (!$productResult) {
    exit('Gagal mengambil data produk.');
}

$products = [];
while ($row = mysqli_fetch_assoc($productResult)) {
    $products[] = $row;
}

$message = '';
$messageType = '';
$transactionStarted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $startDate = $_POST['rental_start_date'] ?? '';
        $endDate = $_POST['rental_end_date'] ?? '';

        if (!$productId || !$quantity || $quantity < 1) {
            throw new Exception('Pilih produk dan masukkan jumlah yang valid.');
        }

        $start = DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
        $end = DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);
        $today = new DateTimeImmutable('today');

        if (!$start || $start->format('Y-m-d') !== $startDate ||
            !$end || $end->format('Y-m-d') !== $endDate) {
            throw new Exception('Tanggal penyewaan tidak valid.');
        }

        if ($start < $today) {
            throw new Exception('Tanggal mulai sewa tidak boleh sebelum hari ini.');
        }

        if ($end < $start) {
            throw new Exception('Tanggal pengembalian harus setelah atau sama dengan tanggal mulai.');
        }

        // Durasi minimal satu hari.
        $rentalDays = max(1, (int)$start->diff($end)->days);

        mysqli_begin_transaction($conn);
        $transactionStarted = true;

        // Kunci baris produk agar stok tidak dipesan melebihi ketersediaan.
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, price_per_day, stock
             FROM products
             WHERE id = ?
             FOR UPDATE"
        );

        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$product) {
            throw new Exception('Produk tidak ditemukan.');
        }

        // Hitung stok yang sudah dipesan pada rentang tanggal tersebut.
        $stmt = mysqli_prepare(
            $conn,
            "SELECT COALESCE(SUM(td.quantity), 0) AS reserved
             FROM transaction_details td
             INNER JOIN transactions t ON t.id = td.transaction_id
             WHERE td.product_id = ?
               AND t.status IN ('pending', 'active')
               AND t.rental_start_date <= ?
               AND t.rental_end_date >= ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $productId,
            $endDate,
            $startDate
        );

        mysqli_stmt_execute($stmt);
        $reservedData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        $availableStock = (int)$product['stock'] - (int)$reservedData['reserved'];

        if ($quantity > $availableStock) {
            throw new Exception(
                'Stok tidak mencukupi. Stok tersedia untuk tanggal tersebut: ' .
                max(0, $availableStock)
            );
        }

        $pricePerDay = (float)$product['price_per_day'];
        $subtotal = $quantity * $rentalDays * $pricePerDay;
        $totalAmount = $subtotal;

        // Buat kode transaksi.
        $transactionCode = 'TRX-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $status = 'pending';

        // Simpan transaksi utama.
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO transactions
             (user_id, transaction_code, rental_start_date,
              rental_end_date, total_amount, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssds",
            $userId,
            $transactionCode,
            $startDate,
            $endDate,
            $totalAmount,
            $status
        );

        mysqli_stmt_execute($stmt);
        $transactionId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Simpan detail produk yang disewa.
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO transaction_details
             (transaction_id, product_id, quantity, rental_days,
              price_per_day, subtotal)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iiiidd",
            $transactionId,
            $productId,
            $quantity,
            $rentalDays,
            $pricePerDay,
            $subtotal
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        $transactionStarted = false;

        $_SESSION['checkout_success'] =
            'Pesanan berhasil dibuat. Kode transaksi: ' . $transactionCode;

        header('Location: checkout.php');
        exit;

    } catch (Throwable $e) {
        if ($transactionStarted) {
            mysqli_rollback($conn);
        }

        $message = $e instanceof mysqli_sql_exception
            ? 'Transaksi gagal disimpan. Periksa struktur database dan coba kembali.'
            : $e->getMessage();

        $messageType = 'error';
    }
}

if (!empty($_SESSION['checkout_success'])) {
    $message = $_SESSION['checkout_success'];
    $messageType = 'success';
    unset($_SESSION['checkout_success']);
}

function rupiah($number): string {
    return 'Rp ' . number_format((float)$number, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdventureAja | Checkout</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #101c2c;
            color: #fff;
            padding: 30px 16px;
        }

        .checkout-container {
            max-width: 850px;
            margin: auto;
        }

        .checkout-header {
            margin-bottom: 25px;
        }

        .checkout-header a {
            color: #ffc107;
            text-decoration: none;
        }

        .checkout-header h1 {
            margin: 18px 0 8px;
        }

        .checkout-card {
            background: #172638;
            border-radius: 14px;
            padding: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 13px;
            border: 1px solid #405168;
            border-radius: 8px;
            background: #101c2c;
            color: #fff;
            font-size: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .product-info {
            padding: 15px;
            background: #20354b;
            border-radius: 9px;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .total-box {
            background: #20354b;
            padding: 18px;
            border-radius: 9px;
            margin: 20px 0;
        }

        .total-box h2 {
            color: #ffc107;
            margin-top: 8px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: #ffc107;
            color: #101c2c;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #e5ad00;
        }

        .message {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .success {
            background: #164c38;
            color: #b9ffdc;
        }

        .error {
            background: #642b32;
            color: #ffd0d0;
        }

        .empty {
            padding: 20px;
            background: #20354b;
            border-radius: 8px;
        }

        @media(max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .checkout-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="checkout-container">

    <div class="checkout-header">
        <a href="user-dashboard.php">Ã¢â€ Â Kembali ke Dashboard</a>
        <h1>Checkout Penyewaan</h1>
        <p>Silakan pilih perlengkapan camping yang ingin disewa.</p>
    </div>

    <div class="checkout-card">

        <?php if ($message): ?>
            <div class="message <?= e($messageType) ?>">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($products)): ?>

            <div class="empty">
                Belum ada produk dengan stok tersedia.
                Silakan hubungi administrator.
            </div>

        <?php else: ?>

            <form method="POST" action="checkout.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="form-group">
                    <label for="product_id">Pilih Perlengkapan</label>

                    <select name="product_id" id="product_id" required>
                        <option value="">-- Pilih Produk --</option>

                        <?php foreach ($products as $product): ?>
                            <option
                                value="<?= (int)$product['id'] ?>"
                                data-price="<?= (float)$product['price_per_day'] ?>"
                                data-stock="<?= (int)$product['stock'] ?>"
                                <?= $selectedProductId === (int)$product['id'] ? 'selected' : '' ?>
                            >
                                <?= e($product['name']) ?>
                                - <?= rupiah($product['price_per_day']) ?>/hari
                                (Stok: <?= (int)$product['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="product-info" id="productInfo">
                    Pilih produk untuk melihat informasi harga dan stok.
                </div>

                <div class="form-group">
                    <label for="quantity">Jumlah Barang</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        value="1"
                        required
                    >
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="rental_start_date">Tanggal Mulai Sewa</label>
                        <input
                            type="date"
                            name="rental_start_date"
                            id="rental_start_date"
                            min="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="rental_end_date">Tanggal Pengembalian</label>
                        <input
                            type="date"
                            name="rental_end_date"
                            id="rental_end_date"
                            min="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="total-box">
                    <p>Estimasi Total Biaya</p>
                    <h2 id="totalDisplay">Rp 0</h2>
                    <small id="durationDisplay">Durasi: 0 hari</small>
                </div>

                <button type="submit" class="btn-submit">
                    Buat Pesanan
                </button>
            </form>

        <?php endif; ?>

    </div>
</div>

<script>
const productSelect = document.getElementById('product_id');
const quantityInput = document.getElementById('quantity');
const startInput = document.getElementById('rental_start_date');
const endInput = document.getElementById('rental_end_date');
const totalDisplay = document.getElementById('totalDisplay');
const durationDisplay = document.getElementById('durationDisplay');
const productInfo = document.getElementById('productInfo');

function formatRupiah(value) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
}

function calculateTotal() {
    if (!productSelect) return;

    const option = productSelect.options[productSelect.selectedIndex];
    const price = Number(option?.dataset.price || 0);
    const stock = Number(option?.dataset.stock || 0);
    const quantity = Math.max(1, Number(quantityInput.value || 1));

    const start = startInput.value
        ? new Date(startInput.value + 'T00:00:00')
        : null;

    const end = endInput.value
        ? new Date(endInput.value + 'T00:00:00')
        : null;

    let days = 0;

    if (start && end && end >= start) {
        days = Math.max(1, Math.round((end - start) / 86400000));
    }

    const total = price * quantity * days;

    totalDisplay.textContent = formatRupiah(total);
    durationDisplay.textContent = 'Durasi: ' + days + ' hari';

    if (productSelect.value) {
        productInfo.textContent =
            'Harga: ' + formatRupiah(price) +
            ' per hari | Stok tercatat: ' + stock;
    } else {
        productInfo.textContent =
            'Pilih produk untuk melihat informasi harga dan stok.';
    }

    quantityInput.max = stock || '';
}

if (productSelect) {
    [productSelect, quantityInput, startInput, endInput].forEach(element => {
        element.addEventListener('change', calculateTotal);
        element.addEventListener('input', calculateTotal);
    });

    startInput.addEventListener('change', function() {
        endInput.min = this.value || '<?= date('Y-m-d') ?>';

        if (endInput.value && endInput.value < this.value) {
            endInput.value = this.value;
        }

        calculateTotal();
    });

    calculateTotal();
}
</script>

</body>
</html>
