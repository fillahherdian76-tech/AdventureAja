<?php
require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once APP_ROOT . '/includes/auth.php';
require_admin();

require_once APP_ROOT . '/config/koneksi.php';

// Mengambil data produk
$sql = "
    SELECT
        p.id,
        p.name,
        p.description,
        p.price_per_day,
        p.stock,
        p.status,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Gagal mengambil data produk: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Produk AdventureAja</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #222;
        }

        h2, p {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background: #172b3b;
            color: white;
            padding: 10px;
        }

        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .aksi {
            margin-bottom: 20px;
            text-align: right;
        }

        button {
            background: #172b3b;
            color: white;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 5px;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
        }

        @media print {
            .aksi {
                display: none;
            }

            body {
                margin: 10px;
            }
        }
    </style>
</head>

<body>

<div class="aksi">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<h2>LAPORAN DATA PRODUK</h2>
<p>AdventureAja - Perlengkapan Camping</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga/Hari</th>
            <th>Stok</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?= $no++ ?></td>

                <td>
                    <?= htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['category_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                </td>

                <td>
                    Rp <?= number_format((float)$row['price_per_day'], 0, ',', '.') ?>
                </td>

                <td><?= (int)$row['stock'] ?></td>

                <td>
                    <?= htmlspecialchars($row['status'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="footer">
    Dicetak pada: <?= date('d-m-Y H:i') ?>
</div>

</body>
</html>
