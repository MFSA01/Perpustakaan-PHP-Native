<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$books = mysqli_query($koneksi, 'SELECT b.*,k.nama_kategori FROM buku b JOIN kategori k ON k.id_kategori=b.id_kategori ORDER BY b.judul');
$page_title = 'Pratinjau Katalog User';
require __DIR__ . '/../includes/header.php'; ?>
<section class="preview-banner">
    <div>
        <p class="eyebrow">MODE PRATINJAU</p>
        <h2>Tampilan katalog anggota</h2>
        <p>Admin dapat melihat buku seperti yang terlihat oleh anggota. Aksi peminjaman tidak tersedia dalam mode ini.</p>
    </div><a class="btn primary" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke admin</a>
</section>
<section class="catalog-grid"><?php while ($b = mysqli_fetch_assoc($books)) { ?><article class="catalog-card">
            <div class="catalog-cover"><img src="../uploads/covers/<?= e($b['cover']) ?>" alt=""><span><?= e($b['kondisi']) ?></span></div>
            <div class="catalog-content"><span class="catalog-category"><?= e($b['nama_kategori']) ?></span>
                <h3><?= e($b['judul']) ?></h3>
                <p class="catalog-author"><i class="fa-regular fa-pen-to-square"></i> <?= e($b['penulis']) ?></p>
                <p class="catalog-stock"><i class="fa-solid fa-cubes-stacked"></i> <?= $b['stok'] ?> eksemplar tersedia</p><a class="book-detail-link" href="detail_buku.php?id=<?= $b['id_buku'] ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat detail & sinopsis</a><button class="btn primary full" disabled><i class="fa-solid fa-eye"></i> Mode pratinjau</button>
            </div>
        </article><?php } ?></section><?php require __DIR__ . '/../includes/footer.php'; ?>