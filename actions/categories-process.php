<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();
require_once APP_ROOT . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categories.php');
    exit;
}

verify_csrf();

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = filter_input(INPUT_POST, 'categoryId', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: categories.php?pesan=id_tidak_valid');
        exit;
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);

    try {
        mysqli_stmt_execute($stmt);
        $deleted = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        header('Location: categories.php?pesan=' . ($deleted ? 'dihapus' : 'tidak_ditemukan'));
    } catch (mysqli_sql_exception $exception) {
        error_log($exception->getMessage());
        mysqli_stmt_close($stmt);
        header('Location: categories.php?pesan=masih_digunakan');
    }
    exit;
}

$name = trim($_POST['categoryName'] ?? '');
$description = trim($_POST['categoryDescription'] ?? '');
$id = filter_input(INPUT_POST, 'categoryId', FILTER_VALIDATE_INT);

if ($name === '' || $description === '' || mb_strlen($name) > 100) {
    header('Location: categories-entry.php?pesan=validasi' . ($id ? '&edit=' . $id : ''));
    exit;
}

// Cegah nama kategori duplikat.
if ($id) {
    $check = mysqli_prepare($conn, 'SELECT id FROM categories WHERE LOWER(name) = LOWER(?) AND id <> ?');
    mysqli_stmt_bind_param($check, 'si', $name, $id);
} else {
    $check = mysqli_prepare($conn, 'SELECT id FROM categories WHERE LOWER(name) = LOWER(?)');
    mysqli_stmt_bind_param($check, 's', $name);
}
mysqli_stmt_execute($check);
$duplicate = mysqli_stmt_get_result($check)->num_rows > 0;
mysqli_stmt_close($check);

if ($duplicate) {
    header('Location: categories-entry.php?pesan=duplikat' . ($id ? '&edit=' . $id : ''));
    exit;
}

if ($id) {
    $stmt = mysqli_prepare($conn, 'UPDATE categories SET name = ?, description = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'ssi', $name, $description, $id);
    $successMessage = 'diperbarui';
} else {
    $stmt = mysqli_prepare($conn, 'INSERT INTO categories (name, description) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ss', $name, $description);
    $successMessage = 'ditambahkan';
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: categories.php?pesan=' . $successMessage);
    exit;
}

error_log(mysqli_error($conn));
mysqli_stmt_close($stmt);
header('Location: categories.php?pesan=gagal');
exit;
?>
