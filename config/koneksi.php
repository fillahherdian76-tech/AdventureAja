<?php
// Koneksi database AdventureAja menggunakan MySQL Laragon.
$host = 'localhost';
$username = 'root';
$password = ''; // Default Laragon biasanya kosong. Ubah jika MySQL Anda memakai password.
$database = 'db_adventureaja';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa pengaturan config/koneksi.php.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>