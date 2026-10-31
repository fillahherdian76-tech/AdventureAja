<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: user-dashboard.php');
    exit;
}

$email = '';
$error = '';

$dataDirectory = __DIR__ . '/data';
$userFile = $dataDirectory . '/users.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($password === '') {
        $error = 'Password wajib diisi.';
    } elseif (!is_file($userFile) || !is_readable($userFile)) {
        $error = 'Data akun belum tersedia. Silakan hubungi admin.';
    } else {
        $contents = file_get_contents($userFile);
        $users = json_decode($contents, true);

        if (!is_array($users)) {
            $users = [];
        }

        $authenticatedUser = null;

        foreach ($users as $user) {
            if (
                isset($user['email'], $user['password']) &&
                strtolower($user['email']) === $email &&
                password_verify($password, $user['password'])
            ) {
                $authenticatedUser = $user;
                break;
            }
        }

        if ($authenticatedUser !== null) {
            session_regenerate_id(true);

            // Simpan informasi yang diperlukan ke session.
            $_SESSION['user'] = [
                'id' => $authenticatedUser['id'] ?? '',
                'name' => $authenticatedUser['name'] ?? '',
                'email' => $authenticatedUser['email'],
                'role' => $authenticatedUser['role'] ?? 'user'
            ];

            header('Location: user-dashboard.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

$registered = isset($_GET['daftar']) &&
              $_GET['daftar'] === 'berhasil';

$loggedOut = isset($_GET['logout']) &&
             $_GET['logout'] === 'berhasil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AdventureAja</title>

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

        .login-wrapper {
            width: 100%;
            max-width: 440px;
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

        .login-card {
            padding: 34px;
            background: rgba(23, 38, 56, .94);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
        }

        .login-card h1 {
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
            margin-bottom: 18px;
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

        .register-text {
            margin-top: 22px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        .register-text a,
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
            .login-card {
                padding: 25px 20px;
            }

            .login-card h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <main class="login-wrapper">
        <a href="index.php" class="brand">
            Adventure<span>Aja</span>
        </a>

        <section class="login-card">
            <h1>Selamat Datang!</h1>
            <p class="subtitle">
                Masuk ke akun AdventureAja untuk melanjutkan petualanganmu.
            </p>

            <?php if ($registered): ?>
                <div class="message success" role="status">
                    Pendaftaran berhasil. Silakan login dengan akun baru.
                </div>
            <?php endif; ?>

            <?php if ($loggedOut): ?>
                <div class="message success" role="status">
                    berhasil logout.
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="message error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
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
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <i
                            class="bx bx-show toggle-password"
                            id="togglePassword"
                            role="button"
                            tabindex="0"
                            aria-label="Tampilkan password"
                        ></i>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Masuk <i class="bx bx-right-arrow-alt"></i>
                </button>
            </form>

            <p class="register-text">
                Belum punya akun?
                <a href="register.php">Daftar sekarang</a>
            </p>

            <a href="index.php" class="back-link">
                <i class="bx bx-arrow-back"></i> Kembali ke Beranda
            </a>
        </section>
    </main>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        function changePasswordVisibility() {
            const showPassword = passwordInput.type === "password";

            passwordInput.type = showPassword ? "text" : "password";
            togglePassword.classList.toggle("bx-show", !showPassword);
            togglePassword.classList.toggle("bx-hide", showPassword);
            togglePassword.setAttribute(
                "aria-label",
                showPassword ? "Sembunyikan password" : "Tampilkan password"
            );
        }

        togglePassword.addEventListener("click", changePasswordVisibility);

        togglePassword.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                changePasswordVisibility();
            }
        });
    </script>
</body>
</html>
