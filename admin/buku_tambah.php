<?php


define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$page_title = 'Tambah Buku';
$errors = [];
$data = ['kondisi' => 'Baik', 'stok' => '0'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $errors = validasi_form_buku($_POST);
    $up = upload_cover_buku($_FILES['cover'] ?? []);
    if (!$up['ok']) $errors[] = $up['message'];
    if (!$errors) {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO buku (id_kategori,kode_buku,judul,penulis,penerbit,tanggal_terbit,kondisi,stok,sinopsis,cover) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $id = (int)$data['id_kategori'];
        $stok = (int)$data['stok'];
        mysqli_stmt_bind_param($stmt, 'issssssiss', $id, $data['kode_buku'], $data['judul'], $data['penulis'], $data['penerbit'], $data['tanggal_terbit'], $data['kondisi'], $stok, $data['sinopsis'], $up['filename']);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Buku berhasil ditambahkan.');
            header('Location: buku.php');
            exit;
        }
        $errors[] = 'Kode buku sudah digunakan.';
    }
}
$kategori = mysqli_query($koneksi, 'SELECT * FROM kategori ORDER BY nama_kategori');
require __DIR__ . '/../includes/header.php'; ?>
<div class="section-head">
    <div>
        <p class="eyebrow">INVENTARIS</p>
        <h2>Tambah buku</h2>
    </div><a class="btn secondary" href="buku.php">Kembali</a>
</div><?php foreach ($errors as $error): ?><div class="alert danger"><?= e($error) ?></div><?php endforeach; ?><?php require __DIR__ . '/form_buku.php';
                                                                                                            require __DIR__ . '/../includes/footer.php';
