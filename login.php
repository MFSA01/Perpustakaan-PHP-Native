<?php

define('APP_ROOT', '');
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($username == '' || $password == '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'SELECT id_user, nama_lengkap, username, role, foto_profil FROM users WHERE username = ? AND password = ? LIMIT 1');
        $hash = md5($password);
        mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$user) {
            $error = 'Username atau password salah.';
        } else {
            foreach ($user as $key => $value) {
                $_SESSION[$key] = $value;
            }
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk · Perpustakaann Farrel</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="auth-page">
    <section class="auth-card">
        <div class="auth-header-logo">
            <img src="assets/img/logo.svg" alt="PF Logo" width="48" height="48">
            <div>
                <b>Perpustakaann Farrel</b>
                <small>SISTEM SIRKULASI BUKU DIGITAL</small>
            </div>
        </div>
        <p class="eyebrow">AKSES SISTEM</p>
        <h1>Masuk ke perpustakaan</h1>
        <p class="muted">Kelola koleksi atau temukan bacaan berikutnya.</p>
        <?php if ($error): ?>
            <div class="alert danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        <form method="post" class="form-stack">
            <label>Username<input name="username" value="<?= isset($_POST['username']) ? e($_POST['username']) : '' ?>" autofocus required></label>
            <label>Password
                <span class="password-field">
                    <input type="password" name="password" id="password" required>
                    <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" title="Tampilkan password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </span>
            </label><button class="btn primary" type="submit">Masuk</button>
        </form>
        <p class="auth-note">Belum punya akun? <a href="register.php">Daftar sebagai anggota</a></p>
        <!-- <p class="credentials">Admin: <b>admin</b> / <b>admin</b></p> -->
    </section>
    <script src="assets/js/app.js"></script>
</body>

</html>