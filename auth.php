<?php
// Fungsi autentikasi dan proteksi halaman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void
{
    if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Akses ditolak. Halaman ini hanya dapat diakses oleh administrator.');
    }
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        exit('Permintaan tidak valid. Muat ulang halaman dan coba lagi.');
    }
}
?>