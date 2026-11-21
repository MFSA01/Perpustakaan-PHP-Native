<?php


define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT b.cover,COUNT(p.id_peminjaman) aktif FROM buku b LEFT JOIN peminjaman p ON p.id_buku=b.id_buku AND p.status IN ('pending','dipinjam') WHERE b.id_buku=? GROUP BY b.id_buku");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$b = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$b || $b['aktif'] > 0) set_flash('danger', 'Buku tidak dapat dihapus karena masih memiliki peminjaman aktif.');
else {
    $stmt = mysqli_prepare($koneksi, 'DELETE FROM buku WHERE id_buku=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt)) {
        if ($b['cover'] !== 'default-cover.jpg') @unlink(__DIR__ . '/../uploads/covers/' . $b['cover']);
        set_flash('success', 'Buku dihapus.');
    }
}
header('Location: buku.php');
exit;
