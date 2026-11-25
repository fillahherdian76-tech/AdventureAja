<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transaction.php');
    exit;
}

verify_csrf();

$action = $_POST['action'] ?? '';
$transactionId = filter_input(INPUT_POST, 'transaction_id', FILTER_VALIDATE_INT);

if ($action === 'status') {
    $status = $_POST['status'] ?? '';
    $allowedStatuses = ['pending', 'active', 'completed', 'cancelled'];

    if (!$transactionId || !in_array($status, $allowedStatuses, true)) {
        header('Location: transaction.php?pesan=gagal');
        exit;
    }

    if (in_array($status, ['pending', 'active'], true)) {
        $started = false;
        try {
            mysqli_begin_transaction($conn);
            $started = true;

            $transactionStmt = mysqli_prepare(
                $conn,
                'SELECT rental_start_date, rental_end_date FROM transactions WHERE id = ? FOR UPDATE'
            );
            mysqli_stmt_bind_param($transactionStmt, 'i', $transactionId);
            mysqli_stmt_execute($transactionStmt);
            $transaction = mysqli_fetch_assoc(mysqli_stmt_get_result($transactionStmt));
            mysqli_stmt_close($transactionStmt);

            if (!$transaction) {
                throw new RuntimeException('Transaksi tidak ditemukan.');
            }

            $detailsStmt = mysqli_prepare(
                $conn,
                'SELECT product_id, quantity FROM transaction_details WHERE transaction_id = ?'
            );
            mysqli_stmt_bind_param($detailsStmt, 'i', $transactionId);
            mysqli_stmt_execute($detailsStmt);
            $details = mysqli_fetch_all(mysqli_stmt_get_result($detailsStmt), MYSQLI_ASSOC);
            mysqli_stmt_close($detailsStmt);

            foreach ($details as $detail) {
                $productId = (int)$detail['product_id'];
                $quantity = (int)$detail['quantity'];

                $productStmt = mysqli_prepare($conn, 'SELECT stock FROM products WHERE id = ? FOR UPDATE');
                mysqli_stmt_bind_param($productStmt, 'i', $productId);
                mysqli_stmt_execute($productStmt);
                $product = mysqli_fetch_assoc(mysqli_stmt_get_result($productStmt));
                mysqli_stmt_close($productStmt);

                $reservedStmt = mysqli_prepare(
                    $conn,
                    "SELECT COALESCE(SUM(td.quantity), 0) AS reserved
                     FROM transaction_details td
                     INNER JOIN transactions t ON t.id = td.transaction_id
                     WHERE td.product_id = ? AND t.id <> ?
                       AND t.status IN ('pending', 'active')
                       AND t.rental_start_date <= ? AND t.rental_end_date >= ?"
                );
                mysqli_stmt_bind_param(
                    $reservedStmt,
                    'iiss',
                    $productId,
                    $transactionId,
                    $transaction['rental_end_date'],
                    $transaction['rental_start_date']
                );
                mysqli_stmt_execute($reservedStmt);
                $reserved = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($reservedStmt))['reserved'];
                mysqli_stmt_close($reservedStmt);

                if (!$product || $reserved + $quantity > (int)$product['stock']) {
                    throw new DomainException('Stok tidak mencukupi.');
                }
            }
        } catch (Throwable $exception) {
            if ($started) {
                mysqli_rollback($conn);
            }
            header('Location: transaction.php?pesan=' . ($exception instanceof mysqli_sql_exception ? 'gagal' : 'stok'));
            exit;
        }

        $stmt = mysqli_prepare($conn, 'UPDATE transactions SET status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $transactionId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        $success = true;
    } else {
        $stmt = mysqli_prepare($conn, 'UPDATE transactions SET status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $transactionId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header('Location: transaction.php?pesan=' . ($success ? 'diperbarui' : 'gagal'));
    exit;
}

if ($action === 'delete') {
    if (!$transactionId) {
        header('Location: transaction.php?pesan=gagal');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM transactions WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $transactionId);

    try {
        mysqli_stmt_execute($stmt);
        $deleted = mysqli_stmt_affected_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        header('Location: transaction.php?pesan=' . ($deleted ? 'dihapus' : 'gagal'));
    } catch (mysqli_sql_exception $exception) {
        error_log($exception->getMessage());
        mysqli_stmt_close($stmt);
        header('Location: transaction.php?pesan=gagal');
    }

    exit;
}

if ($action !== 'create') {
    header('Location: transaction.php?pesan=gagal');
    exit;
}

$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$startDate = $_POST['rental_start_date'] ?? '';
$endDate = $_POST['rental_end_date'] ?? '';
$start = DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
$end = DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);

if (!$userId || !$productId || !$quantity || $quantity < 1 ||
    !$start || $start->format('Y-m-d') !== $startDate ||
    !$end || $end->format('Y-m-d') !== $endDate || $start < new DateTimeImmutable('today') || $end < $start) {
    header('Location: transaction-entry.php?pesan=validasi');
    exit;
}

$rentalDays = max(1, (int)$start->diff($end)->days);
$transactionStarted = false;

try {
    mysqli_begin_transaction($conn);
    $transactionStarted = true;

    $userStmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE id = ? AND role = \'user\'');
    mysqli_stmt_bind_param($userStmt, 'i', $userId);
    mysqli_stmt_execute($userStmt);
    $userExists = mysqli_stmt_get_result($userStmt)->num_rows > 0;
    mysqli_stmt_close($userStmt);

    if (!$userExists) {
        throw new RuntimeException('Pengguna tidak ditemukan.');
    }

    $productStmt = mysqli_prepare($conn, "SELECT id, price_per_day, stock FROM products WHERE id = ? AND status = 'active' FOR UPDATE");
    mysqli_stmt_bind_param($productStmt, 'i', $productId);
    mysqli_stmt_execute($productStmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($productStmt));
    mysqli_stmt_close($productStmt);

    if (!$product) {
        throw new RuntimeException('Produk tidak tersedia.');
    }

    $reservedStmt = mysqli_prepare(
        $conn,
        "SELECT COALESCE(SUM(td.quantity), 0) AS reserved
         FROM transaction_details td
         INNER JOIN transactions t ON t.id = td.transaction_id
         WHERE td.product_id = ?
           AND t.status IN ('pending', 'active')
           AND t.rental_start_date <= ?
           AND t.rental_end_date >= ?"
    );
    mysqli_stmt_bind_param($reservedStmt, 'iss', $productId, $endDate, $startDate);
    mysqli_stmt_execute($reservedStmt);
    $reserved = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($reservedStmt))['reserved'];
    mysqli_stmt_close($reservedStmt);

    if ($quantity > (int)$product['stock'] - $reserved) {
        throw new RuntimeException('Stok tidak mencukupi untuk rentang tanggal tersebut.');
    }

    $price = (float)$product['price_per_day'];
    $subtotal = $price * $quantity * $rentalDays;
    $status = 'pending';
    $code = 'TRX-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));

    $insertTransaction = mysqli_prepare(
        $conn,
        'INSERT INTO transactions (user_id, transaction_code, rental_start_date, rental_end_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($insertTransaction, 'isssds', $userId, $code, $startDate, $endDate, $subtotal, $status);
    mysqli_stmt_execute($insertTransaction);
    $newTransactionId = mysqli_insert_id($conn);
    mysqli_stmt_close($insertTransaction);

    $insertDetail = mysqli_prepare(
        $conn,
        'INSERT INTO transaction_details (transaction_id, product_id, quantity, rental_days, price_per_day, subtotal) VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($insertDetail, 'iiiidd', $newTransactionId, $productId, $quantity, $rentalDays, $price, $subtotal);
    mysqli_stmt_execute($insertDetail);
    mysqli_stmt_close($insertDetail);

    mysqli_commit($conn);
    $transactionStarted = false;
    header('Location: transaction.php?pesan=ditambahkan');
    exit;
} catch (Throwable $exception) {
    if ($transactionStarted) {
        mysqli_rollback($conn);
    }

    if ($exception instanceof mysqli_sql_exception) {
        error_log($exception->getMessage());
        header('Location: transaction.php?pesan=gagal');
    } else {
        header('Location: transaction-entry.php?pesan=validasi');
    }
    exit;
}
