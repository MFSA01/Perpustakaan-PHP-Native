<?php

define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$page_title = 'Koleksi Buku';
$cari = trim($_GET['cari'] ?? '');
$kategori = (int)($_GET['kategori'] ?? 0);
$sql = 'SELECT b.*,k.nama_kategori FROM buku b JOIN kategori k ON k.id_kategori=b.id_kategori WHERE 1=1';
$types = '';
$params = [];
if ($cari !== '') {
    $sql .= ' AND (b.judul LIKE ? OR b.penulis LIKE ? OR b.kode_buku LIKE ?)';
    $like = "%$cari%";
    $types .= 'sss';
    $params = [$like, $like, $like];
}
if ($kategori > 0) {
    $sql .= ' AND b.id_kategori=?';
    $types .= 'i';
    $params[] = $kategori;
}
$sql .= ' ORDER BY b.created_at DESC';
$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$buku = mysqli_stmt_get_result($stmt);
$kategoriList = mysqli_query($koneksi, 'SELECT * FROM kategori ORDER BY nama_kategori');
require __DIR__ . '/../includes/header.php'; ?>
<div class="section-head">
    <div>
        <p class="eyebrow">INVENTARIS</p>
        <h2>Daftar koleksi</h2>
    </div><a class="btn primary" href="buku_tambah.php">Tambah buku</a>
</div>
<form class="filters" method="get">
    <input name="cari" value="<?= e($cari) ?>" placeholder="Cari judul, penulis, kode">
    <select name="kategori">
        <option value="0">Semua kategori</option>
        <?php while ($k = mysqli_fetch_assoc($kategoriList)): ?>
            <option value="<?= $k['id_kategori'] ?>" <?= $kategori === $k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
        <?php endwhile; ?>
    </select>
    <button class="btn secondary">Filter</button>
</form>
<section class="panel table-wrap">
    <table>
        <thead>
            <tr>
                <th>Cover</th>
                <th>Kode / Judul</th>
                <th>Kategori</th>
                <th>Kondisi</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($buku)): ?>
                <tr>
                    <td><img class="thumb" src="../uploads/covers/<?= e($row['cover']) ?>" alt=""></td>
                    <td><b><?= e($row['judul']) ?></b><small><?= e($row['kode_buku']) ?> · <?= e($row['penulis']) ?></small></td>
                    <td><?= e($row['nama_kategori']) ?></td>
                    <td><?= e($row['kondisi']) ?></td>
                    <td><?= e((string)$row['stok']) ?></td>
                    <td class="actions">
                        <a href="buku_edit.php?id=<?= $row['id_buku'] ?>">Edit</a>
                        <a class="danger-link" data-confirm="Hapus buku ini?" href="buku_hapus.php?id=<?= $row['id_buku'] ?>">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php';
