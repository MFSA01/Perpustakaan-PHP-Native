<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('user');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$id = (int) $_SESSION['id_user'];
$stats = array(
    array('Pengajuan Pending', query_count($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_user=$id AND status='pending'"), 'fa-hourglass-half'),
    array('Sedang Dipinjam', query_count($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_user=$id AND status='dipinjam'"), 'fa-book-open'),
    array('Riwayat Selesai', query_count($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_user=$id AND status='dikembalikan'"), 'fa-circle-check'),
    array('Total Denda', format_rupiah(query_count($koneksi, "SELECT COALESCE(SUM(denda),0) FROM peminjaman WHERE id_user=$id")), 'fa-wallet')
);
$pinjam = mysqli_query($koneksi, "SELECT p.*, b.judul, b.cover FROM peminjaman p JOIN buku b ON b.id_buku=p.id_buku WHERE p.id_user=$id AND p.status='dipinjam'");
$popular = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON k.id_kategori=b.id_kategori ORDER BY b.stok DESC LIMIT 4");
$page_title = 'Ringkasan Saya';
require __DIR__ . '/../includes/header.php';
?>
<section class="dashboard-layout">
    <div class="dashboard-main">
        <section class="hero-library"><div><p class="eyebrow">SELAMAT DATANG, <?= e(strtoupper(explode(' ', $_SESSION['nama_lengkap'])[0])) ?></p><h2>Temukan cerita<br>untuk hari ini.</h2><p>Jelajahi koleksi pilihan dan ajukan buku yang ingin Anda baca.</p><a class="hero-button" href="katalog.php">Jelajahi katalog <i class="fa-solid fa-arrow-right"></i></a></div><div class="hero-illustration"><i class="fa-solid fa-book-open"></i><span></span><i class="fa-solid fa-feather-pointed"></i></div></section>
        <section class="section-title"><div><h2>Buku populer</h2><p>Koleksi yang siap menemani waktu baca.</p></div><a href="katalog.php">Lihat semua <i class="fa-solid fa-arrow-right"></i></a></section>
        <section class="featured-books"><?php while ($book = mysqli_fetch_assoc($popular)) { ?><article class="featured-book"><img src="../uploads/covers/<?= e($book['cover']) ?>" alt=""><div><span><?= e($book['nama_kategori']) ?></span><h3><?= e($book['judul']) ?></h3><p><?= e($book['penulis']) ?></p><small><i class="fa-solid fa-book"></i> <?= e((string) $book['stok']) ?> tersedia</small></div></article><?php } ?></section>
        <section class="mini-stats"><?php foreach ($stats as $stat) { ?><article><span class="stat-icon"><i class="fa-solid <?= e($stat[2]) ?>"></i></span><div><b><?= e((string) $stat[1]) ?></b><small><?= e($stat[0]) ?></small></div></article><?php } ?></section>
    </div>
    <aside class="dashboard-side"><section class="side-card notice-card"><span class="side-icon"><i class="fa-regular fa-bell"></i></span><h3>Perpustakaann Farrel</h3><p>Ruang baca digital untuk menemukan gagasan dan pengetahuan baru.</p><a href="katalog.php">Lihat koleksi</a></section><section class="side-card"><div class="section-title compact"><div><h2>Sedang dipinjam</h2><p>Perhatikan tanggal kembali.</p></div><a href="riwayat.php"><i class="fa-solid fa-arrow-right"></i></a></div><?php $ada = false; while ($row = mysqli_fetch_assoc($pinjam)) { $ada = true; $late = max(0, (int) floor((time() - strtotime($row['tanggal_jatuh_tempo'])) / 86400)); ?><div class="side-loan"><img src="../uploads/covers/<?= e($row['cover']) ?>" alt=""><div><b><?= e($row['judul']) ?></b><small>Tempo <?= format_tanggal_id($row['tanggal_jatuh_tempo']) ?></small></div><span class="loan-status <?= $late ? 'late' : '' ?>"><?= $late ? 'Terlambat' : 'Aktif' ?></span></div><?php } if (!$ada) { ?><p class="empty-note">Belum ada buku yang sedang dipinjam.</p><?php } ?></section></aside>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
