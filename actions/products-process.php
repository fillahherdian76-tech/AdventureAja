<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

verify_csrf();

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: products.php?pesan=tidak_ditemukan');
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM products WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, 'i', $id);

    try {
        mysqli_stmt_execute($stmt);
        $deleted = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        header(
            'Location: products.php?pesan=' .
            ($deleted ? 'dihapus' : 'tidak_ditemukan')
        );
    } catch (mysqli_sql_exception $exception) {
        error_log($exception->getMessage());
        mysqli_stmt_close($stmt);

        header('Location: products.php?pesan=gagal');
    }

    exit;
}

$id = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT);

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = filter_input(INPUT_POST, 'price_per_day', FILTER_VALIDATE_FLOAT);
$stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if (
    $name === '' ||
    mb_strlen($name) > 150 ||
    $price === false ||
    $price === null ||
    $price < 0 ||
    $stock === false ||
    $stock === null ||
    $stock < 0 ||
    !in_array($status, ['active', 'inactive'], true)
) {
    header(
        'Location: products-entry.php?pesan=validasi' .
        ($id ? '&edit=' . $id : '')
    );
    exit;
}

// Kategori dipilih otomatis agar form produk tidak meminta kategori.
if ($id) {
    $existingProduct = mysqli_prepare($conn, 'SELECT category_id FROM products WHERE id = ?');
    mysqli_stmt_bind_param($existingProduct, 'i', $id);
    mysqli_stmt_execute($existingProduct);
    $existingData = mysqli_fetch_assoc(mysqli_stmt_get_result($existingProduct));
    mysqli_stmt_close($existingProduct);

    if (!$existingData) {
        header('Location: products.php?pesan=tidak_ditemukan');
        exit;
    }

    $categoryId = (int)$existingData['category_id'];
} else {
    $defaultCategoryName = 'Lainnya';
    $categoryStmt = mysqli_prepare($conn, 'SELECT id FROM categories WHERE LOWER(name) = LOWER(?) ORDER BY id LIMIT 1');
    mysqli_stmt_bind_param($categoryStmt, 's', $defaultCategoryName);
    mysqli_stmt_execute($categoryStmt);
    $defaultCategory = mysqli_fetch_assoc(mysqli_stmt_get_result($categoryStmt));
    mysqli_stmt_close($categoryStmt);

    if (!$defaultCategory) {
        $defaultDescription = 'Kategori otomatis untuk produk yang tidak memerlukan kategori khusus.';
        $categoryStmt = mysqli_prepare($conn, 'INSERT INTO categories (name, description) VALUES (?, ?)');
        mysqli_stmt_bind_param($categoryStmt, 'ss', $defaultCategoryName, $defaultDescription);
        mysqli_stmt_execute($categoryStmt);
        $categoryId = mysqli_insert_id($conn);
        mysqli_stmt_close($categoryStmt);
    } else {
        $categoryId = (int)$defaultCategory['id'];
    }
}

$imagePath = '';
$uploadedImagePath = null;
$imageUpload = $_FILES['image'] ?? null;

if ($imageUpload && $imageUpload['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($imageUpload['error'] !== UPLOAD_ERR_OK || $imageUpload['size'] > 5 * 1024 * 1024) {
        header('Location: products-entry.php?pesan=gambar' . ($id ? '&edit=' . $id : ''));
        exit;
    }

    $imageTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($imageUpload['tmp_name']);
    $imageDetails = @getimagesize($imageUpload['tmp_name']);

    if (!isset($imageTypes[$mimeType]) || !$imageDetails) {
        header('Location: products-entry.php?pesan=gambar' . ($id ? '&edit=' . $id : ''));
        exit;
    }

    $uploadDirectory = APP_ROOT . '/uploads/products';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        header('Location: products-entry.php?pesan=gambar' . ($id ? '&edit=' . $id : ''));
        exit;
    }

    $fileName = 'product-' . bin2hex(random_bytes(12)) . '.' . $imageTypes[$mimeType];
    $uploadedImagePath = $uploadDirectory . '/' . $fileName;

    if (!move_uploaded_file($imageUpload['tmp_name'], $uploadedImagePath)) {
        header('Location: products-entry.php?pesan=gambar' . ($id ? '&edit=' . $id : ''));
        exit;
    }

    $imagePath = 'uploads/products/' . $fileName;
}

if ($id) {
    if ($imagePath !== '') {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET category_id = ?, name = ?, description = ?,
                 price_per_day = ?, stock = ?, status = ?, image = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            'issdissi',
            $categoryId,
            $name,
            $description,
            $price,
            $stock,
            $status,
            $imagePath,
            $id
        );
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET category_id = ?, name = ?, description = ?,
                 price_per_day = ?, stock = ?, status = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            'issdisi',
            $categoryId,
            $name,
            $description,
            $price,
            $stock,
            $status,
            $id
        );
    }

    $successMessage = 'diperbarui';

} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products
         (category_id, name, description, price_per_day, stock, status, image)
         VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, ''))"
    );

    mysqli_stmt_bind_param(
        $stmt,
        'issdiss',
        $categoryId,
        $name,
        $description,
        $price,
        $stock,
        $status,
        $imagePath
    );

    $successMessage = 'ditambahkan';
}

try {
    $saved = mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    mysqli_stmt_close($stmt);

    if ($uploadedImagePath && is_file($uploadedImagePath)) {
        unlink($uploadedImagePath);
    }

    header('Location: products.php?pesan=gagal');
    exit;
}

if ($saved) {
    mysqli_stmt_close($stmt);

    header('Location: products.php?pesan=' . $successMessage);
    exit;
}

error_log(mysqli_stmt_error($stmt));
mysqli_stmt_close($stmt);

if ($uploadedImagePath && is_file($uploadedImagePath)) {
    unlink($uploadedImagePath);
}

header('Location: products.php?pesan=gagal');
exit;
