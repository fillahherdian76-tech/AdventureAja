<?php
session_start();

// Kalau sudah login, arahkan ke dashboard.
if (isset($_SESSION['user'])) {
    header('Location: user-dashboard.php');
    exit;
}

$errors = [];
$success = '';

$fullName = '';
$email = '';

// Lokasi penyimpanan akun.
$dataDirectory = __DIR__ . '/data';
$userFile = $dataDirectory . '/users.json';

// Buat folder data jika belum tersedia.
if (!is_dir($dataDirectory)) {
    mkdir($dataDirectory, 0755, true);
}

// Buat file akun kosong jika belum tersedia.
if (!file_exists($userFile)) {
    file_put_contents($userFile, '[]');
}

// Proses form ketika tombol Daftar ditekan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (mb_strlen($fullName) < 3) {
        $errors[] = 'Nama lengkap minimal 3 karakter.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        $fileHandle = fopen($userFile, 'c+');

        if ($fileHandle === false) {
            $errors[] = 'File akun tidak dapat dibuka.';
        } else {
            // Mengunci file agar penulisan data lebih aman.
            if (flock($fileHandle, LOCK_EX)) {
                rewind($fileHandle);
                $contents = stream_get_contents($fileHandle);
                $users = json_decode($contents ?: '[]', true);

                if (!is_array($users)) {
                    $users = [];
                }

                $emailExists = false;

                foreach ($users as $user) {
                    if (
                        isset($user['email']) &&
                        strtolower($user['email']) === $email
                    ) {
                        $emailExists = true;
                        break;
                    }
                }

                if ($emailExists) {
                    $errors[] = 'Email sudah terdaftar. Gunakan email lain.';
                } else {
                    $users[] = [
                        'id' => uniqid('user_', true),
                        'name' => $fullName,
                        'email' => $email,
                        'password' => password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        ),
                        'role' => 'user'
                    ];

                    $json = json_encode(
                        $users,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                    );

                    rewind($fileHandle);
                    ftruncate($fileHandle, 0);

                    if (fwrite($fileHandle, $json) === false) {
                        $errors[] = 'Akun gagal disimpan.';
                    } else {
                        fflush($fileHandle);
                        $success = 'Pendaftaran berhasil! Mengalihkan ke halaman login...';
                    }
                }

                flock($fileHandle, LOCK_UN);
            } else {
                $errors[] = 'Data akun sedang digunakan. Coba lagi.';
            }

            fclose($fileHandle);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | AdventureAja</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
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
            --white: #ffffff;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 16px;
            color: var(--white);
            background:
                linear-gradient(rgba(7, 16, 29, .90), rgba(7, 16, 29, .96)),
                url("assets/backgrounddashboard.jpg") center/cover fixed;
        }

        .register-wrapper {
            width: 100%;
            max-width: 480px;
        }

        .brand {
            display: block;
            margin-bottom: 22px;
            text-align: center;
            color: var(--white);
            text-decoration: none;
            font-size: 28px;
            font-weight: 800;
        }

        .brand span {
            color: var(--yellow);
        }

        .register-card {
            padding: 34px;
            background: rgba(23, 38, 56, .94);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
        }

        .register-card h1 {
            font-size: 26px;
            margin-bottom: 7px;
        }

        .subtitle {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 500;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap > i:first-child {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--yellow);
            font-size: 19px;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 42px;
            color: var(--white);
            background: #0f1b2a;
            border: 1px solid #344356;
            border-radius: 9px;
            outline: none;
            font-size: 13px;
        }

        .input-wrap input:focus {
            border-color: var(--yellow);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, .10);
        }

        .input-wrap input::placeholder {
            color: #7f8da0;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            cursor: pointer;
            font-size: 19px;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            margin-top: 5px;
            border: none;
            border-radius: 9px;
            background: var(--yellow);
            color: #182333;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        .submit-btn:hover {
            background: #ffcf40;
            transform: translateY(-1px);
        }

        .message {
            margin-bottom: 17px;
            padding: 11px 13px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.6;
        }

        .message.error {
            color: #ffd6d6;
            background: rgba(220, 53, 69, .15);
            border: 1px solid rgba(220, 53, 69, .4);
        }

        .message.success {
            color: #d5ffe4;
            background: rgba(40, 167, 69, .15);
            border: 1px solid rgba(40, 167, 69, .4);
        }

        .login-text {
            margin-top: 22px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        .login-text a,
        .back-link {
            color: var(--yellow);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 25px 20px;
            }

            .register-card h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="register-wrapper">
        <a href="index.php" class="brand">
            Adventure<span>Aja</span>
        </a>

        <section class="register-card">
            <h1>Buat Akun Baru</h1>
            <p class="subtitle">
                Bergabunglah dengan AdventureAja dan persiapkan petualanganmu!
            </p>

            <?php if (!empty($errors)): ?>
                <div class="message error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <div class="message success" role="status">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="register.php">
                <div class="form-group">
                    <label for="fullName">Nama Lengkap</label>
                    <div class="input-wrap">
                        <i class="bx bx-user"></i>
                        <input
                            type="text"
                            id="fullName"
                            name="fullName"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            minlength="3"
                            value="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="bx bx-envelope"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="bx bx-lock-alt"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <i
                            class="bx bx-show toggle-password"
                            data-target="password"
                            role="button"
                            tabindex="0"
                            aria-label="Tampilkan password"
                        ></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <i class="bx bx-lock"></i>
                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <i
                            class="bx bx-show toggle-password"
                            data-target="confirmPassword"
                            role="button"
                            tabindex="0"
                            aria-label="Tampilkan konfirmasi password"
                        ></i>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Daftar Sekarang <i class="bx bx-right-arrow-alt"></i>
                </button>
            </form>

            <p class="login-text">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </p>

            <a href="index.php" class="back-link">
                <i class="bx bx-arrow-back"></i> Kembali ke Beranda
            </a>
        </section>
    </main>

    <script>
        // Menampilkan atau menyembunyikan password.
        document.querySelectorAll(".toggle-password").forEach(function (icon) {
            function togglePassword() {
                const input = document.getElementById(icon.dataset.target);
                const isPassword = input.type === "password";

                input.type = isPassword ? "text" : "password";
                icon.classList.toggle("bx-show", !isPassword);
                icon.classList.toggle("bx-hide", isPassword);

                icon.setAttribute(
                    "aria-label",
                    isPassword ? "Sembunyikan password" : "Tampilkan password"
                );
            }

            icon.addEventListener("click", togglePassword);

            icon.addEventListener("keydown", function (event) {
                if (event.key === "Enter" || event.key === " ") {
                    event.preventDefault();
                    togglePassword();
                }
            });
        });

        // Setelah pendaftaran berhasil, arahkan ke halaman login.
        <?php if ($success !== ''): ?>
            setTimeout(function () {
                window.location.href = "login.php?daftar=berhasil";
            }, 1200);
        <?php endif; ?>
    </script>
</body>
</html>
