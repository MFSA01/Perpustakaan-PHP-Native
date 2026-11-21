<?php


define('APP_ROOT', '');
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp = trim($_POST['no_telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    if ($nama === '' || $username === '' || $email === '' || $password === '') $errors[] = 'Semua kolom wajib selain telepon harus diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $konfirmasi) $errors[] = 'Konfirmasi password tidak cocok.';
    if (!$errors) {
        $stmt = mysqli_prepare($koneksi, 'SELECT id_user FROM users WHERE username=? OR email=?');
        mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
        mysqli_stmt_execute($stmt);
        $ada = mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0;
        mysqli_stmt_close($stmt);
        if ($ada) $errors[] = 'Username atau email sudah digunakan.';
        else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap,username,password,email,no_telepon,role) VALUES (?,?,?,?,?,'user')");
            $hash = md5($password);
            mysqli_stmt_bind_param($stmt, 'sssss', $nama, $username, $hash, $email, $telp);
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Registrasi berhasil. Silakan masuk.');
                header('Location: login.php');
                exit;
            }
            $errors[] = 'Registrasi gagal.';
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar · Perpustakaann Farrel</title>
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
                <small>PENDAFTARAN ANGGOTA BARU</small>
            </div>
        </div>
        <p class="eyebrow">ANGGOTA BARU</p>
        <h1>Buat akun anggota</h1><?php foreach ($errors as $error): ?><div class="alert danger"><?= e($error) ?></div><?php endforeach; ?><form method="post" class="form-stack"><label>Nama lengkap<input name="nama_lengkap" value="<?= e($_POST['nama_lengkap'] ?? '') ?>" required></label><label>Username<input name="username" value="<?= e($_POST['username'] ?? '') ?>" required></label><label>Email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></label><label>No. telepon <input name="no_telepon" value="<?= e($_POST['no_telepon'] ?? '') ?>"></label><label>Password<span class="password-field"><input type="password" name="password" id="password" required><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" title="Tampilkan password"><i class="fa-solid fa-eye"></i></button></span></label><label>Konfirmasi password<span class="password-field"><input type="password" name="konfirmasi_password" id="konfirmasi_password" required><button class="password-toggle" type="button" data-password-toggle="konfirmasi_password" aria-label="Tampilkan konfirmasi password" title="Tampilkan password"><i class="fa-solid fa-eye"></i></button></span></label><button class="btn primary" type="submit">Daftar akun</button></form>
        <p class="auth-note">Sudah punya akun? <a href="login.php">Masuk</a></p>
    </section><script src="assets/js/app.js"></script>
</body>

</html>
