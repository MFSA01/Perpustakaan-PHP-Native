<?php


define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, 'SELECT * FROM buku WHERE id_buku=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$data) {
    set_flash('danger', 'Buku tidak ditemukan.');
    header('Location: buku.php');
    exit;
}
$page_title = 'Edit Buku';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($data, $_POST);
    $errors = validasi_form_buku($_POST);
    $up = upload_cover_buku($_FILES['cover'] ?? [], $data['cover']);
    if (!$up['ok']) $errors[] = $up['message'];
    if (!$errors) {
        $stmt = mysqli_prepare($koneksi, 'UPDATE buku SET id_kategori=?,kode_buku=?,judul=?,penulis=?,penerbit=?,tanggal_terbit=?,kondisi=?,stok=?,sinopsis=?,cover=? WHERE id_buku=?');
        $kat = (int)$data['id_kategori'];
        $stok = (int)$data['stok'];
        mysqli_stmt_bind_param($stmt, 'issssssissi', $kat, $data['kode_buku'], $data['judul'], $data['penulis'], $data['penerbit'], $data['tanggal_terbit'], $data['kondisi'], $stok, $data['sinopsis'], $up['filename'], $id);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Buku diperbarui.');
            header('Location: buku.php');
            exit;
        }
        $errors[] = 'Kode buku sudah digunakan.';
    }
}
$kategori = mysqli_query($koneksi, 'SELECT * FROM kategori ORDER BY nama_kategori');
require __DIR__ . '/../includes/header.php'; ?><div class="section-head">
    <div>
        <p class="eyebrow">INVENTARIS</p>
        <h2>Edit buku</h2>
    </div><a class="btn secondary" href="buku.php">Kembali</a>
</div><?php foreach ($errors as $error): ?><div class="alert danger"><?= e($error) ?></div><?php endforeach;
                                                                                        require __DIR__ . '/form_buku.php';
                                                                                        require __DIR__ . '/../includes/footer.php';
