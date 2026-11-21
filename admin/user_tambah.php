<?php
define('APP_ROOT', '..'); require __DIR__ . '/../includes/auth.php'; require_role('admin'); require __DIR__ . '/../config/database.php'; require __DIR__ . '/../includes/functions.php';
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama_lengkap']); $username = trim($_POST['username']); $email = trim($_POST['email']); $telp = trim($_POST['no_telepon']); $password = $_POST['password'];
    if ($nama == '' || $username == '' || $email == '' || strlen($password) < 6) $errors[] = 'Lengkapi data. Password minimal 6 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (count($errors) == 0) { $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap,username,password,email,no_telepon,role) VALUES (?,?,?,?,?,'user')"); $hash = md5($password); mysqli_stmt_bind_param($stmt, 'sssss', $nama, $username, $hash, $email, $telp); if (mysqli_stmt_execute($stmt)) { set_flash('success', 'Anggota berhasil ditambahkan.'); header('Location: anggota.php'); exit; } $errors[] = 'Username atau email sudah digunakan.'; }
}
$page_title = 'Tambah Anggota'; require __DIR__ . '/../includes/header.php'; ?>
<div class="section-head"><div><p class="eyebrow">DATA ANGGOTA</p><h2>Tambah anggota baru</h2></div><a class="btn secondary" href="anggota.php">Kembali</a></div><?php foreach ($errors as $error) { ?><div class="alert danger"><?= e($error) ?></div><?php } ?><form class="panel form-stack member-form" method="post"><label>Nama lengkap<input name="nama_lengkap" required></label><label>Username<input name="username" required></label><label>Email<input type="email" name="email" required></label><label>No. telepon<input name="no_telepon"></label><label>Password<input type="password" name="password" required></label><button class="btn primary">Simpan anggota</button></form><?php require __DIR__ . '/../includes/footer.php'; ?>
