<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == (int)$_SESSION['id_user']) set_flash('danger', 'Akun sendiri tidak dapat dihapus.');
else {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user=? AND role='user'");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    set_flash(mysqli_stmt_affected_rows($stmt) ? 'success' : 'danger', mysqli_stmt_affected_rows($stmt) ? 'Anggota dan riwayatnya dihapus.' : 'Anggota tidak dapat dihapus.');
}
header('Location: anggota.php');
exit;
