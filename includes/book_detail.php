<?php
// Halaman detail buku dipakai admin dan user. Variabel $role_folder wajib diisi pemanggil.
$bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = mysqli_prepare($koneksi, 'SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON k.id_kategori=b.id_kategori WHERE b.id_buku=?');
mysqli_stmt_bind_param($stmt, 'i', $bookId);
mysqli_stmt_execute($stmt);
$book = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$book) {
    set_flash('danger', 'Buku tidak ditemukan.');
    header('Location: katalog.php');
    exit;
}

$canBorrow = false;
$buttonText = 'Mode pratinjau admin';
if ($role_folder == 'user') {
    $userId = (int) $_SESSION['id_user'];
    $check = mysqli_prepare($koneksi, "SELECT COUNT(*) total FROM peminjaman WHERE id_user=? AND id_buku=? AND status IN ('pending','dipinjam')");
    mysqli_stmt_bind_param($check, 'ii', $userId, $bookId);
    mysqli_stmt_execute($check);
    $active = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($check))['total'];
    $canBorrow = $book['stok'] > 0 && $active == 0;
    if ($active > 0) $buttonText = 'Sedang diproses';
    elseif ($book['stok'] < 1) $buttonText = 'Stok habis';
    else $buttonText = 'Ajukan peminjaman';
}
?>
<section class="detail-book"><div class="detail-cover"><img src="../uploads/covers/<?= e($book['cover']) ?>" alt="Cover <?= e($book['judul']) ?>"></div><div class="detail-info"><a class="back-link" href="<?= $role_folder == 'admin' ? 'tampilan_user.php' : 'katalog.php' ?>"><i class="fa-solid fa-arrow-left"></i> Kembali ke katalog</a><span class="catalog-category"><?= e($book['nama_kategori']) ?></span><h2><?= e($book['judul']) ?></h2><p class="detail-author"><i class="fa-regular fa-pen-to-square"></i> <?= e($book['penulis']) ?></p><div class="detail-meta"><span><i class="fa-solid fa-barcode"></i> <?= e($book['kode_buku']) ?></span><span><i class="fa-solid fa-building"></i> <?= e($book['penerbit']) ?></span><span><i class="fa-regular fa-calendar"></i> <?= format_tanggal_id($book['tanggal_terbit']) ?></span><span><i class="fa-solid fa-cubes-stacked"></i> <?= e((string) $book['stok']) ?> tersedia</span></div><div class="detail-action"><?php if ($role_folder == 'user') { ?><form method="post" action="katalog.php"><input type="hidden" name="id_buku" value="<?= $book['id_buku'] ?>"><button class="btn primary" <?= !$canBorrow ? 'disabled' : '' ?>><i class="fa-solid fa-bookmark"></i> <?= $buttonText ?></button></form><?php } else { ?><a class="btn secondary" href="buku_edit.php?id=<?= $book['id_buku'] ?>"><i class="fa-solid fa-pen"></i> Edit buku</a><?php } ?></div></div></section><section class="panel synopsis-panel"><p class="eyebrow">TENTANG BUKU</p><h2>Sinopsis</h2><p><?= nl2br(e($book['sinopsis'])) ?></p></section>
