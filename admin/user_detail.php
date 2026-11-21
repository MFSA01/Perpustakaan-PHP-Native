<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = mysqli_prepare($koneksi, 'SELECT * FROM users WHERE id_user=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$user) {
    set_flash('danger', 'Anggota tidak ditemukan.');
    header('Location: anggota.php');
    exit;
}
$stats = mysqli_query($koneksi, "SELECT COUNT(*) total, SUM(status='pending') pending, SUM(status='dipinjam') dipinjam, SUM(status='dikembalikan') selesai, COALESCE(SUM(hari_terlambat),0) terlambat, COALESCE(SUM(denda),0) denda FROM peminjaman WHERE id_user=$id");
$stat = mysqli_fetch_assoc($stats);
$history = mysqli_query($koneksi, "SELECT p.*,b.judul,b.kode_buku,b.cover FROM peminjaman p JOIN buku b ON b.id_buku=p.id_buku WHERE p.id_user=$id ORDER BY p.created_at DESC");
$page_title = 'Detail Anggota';
require __DIR__ . '/../includes/header.php'; ?>
<section class="member-profile">
    <div class="member-avatar"><?= e(strtoupper(substr($user['nama_lengkap'], 0, 1))) ?></div>
    <div>
        <p class="eyebrow">PROFIL ANGGOTA</p>
        <h2><?= e($user['nama_lengkap']) ?></h2>
        <p>@<?= e($user['username']) ?> · <?= e($user['email']) ?></p><small><i class="fa-solid fa-phone"></i> <?= e($user['no_telepon']) ?> &nbsp; <i class="fa-regular fa-calendar"></i> Terdaftar <?= format_tanggal_id($user['created_at']) ?></small>
    </div>
    <div class="member-actions"><a class="btn secondary" href="user_edit.php?id=<?= $id ?>"><i class="fa-solid fa-pen"></i> Edit</a><a class="danger-link" data-confirm="Menghapus anggota juga menghapus seluruh riwayat peminjamannya. Lanjutkan?" href="user_hapus.php?id=<?= $id ?>">Hapus anggota</a></div>
</section>
<section class="member-stats">
    <article><i class="fa-solid fa-book"></i><b><?= $stat['total'] ?></b><span>Total transaksi</span></article>
    <article><i class="fa-solid fa-hourglass-half"></i><b><?= $stat['pending'] ?></b><span>Pengajuan pending</span></article>
    <article><i class="fa-solid fa-book-open"></i><b><?= $stat['dipinjam'] ?></b><span>Sedang dipinjam</span></article>
    <article><i class="fa-solid fa-clock"></i><b><?= $stat['terlambat'] ?></b><span>Hari terlambat</span></article>
    <article><i class="fa-solid fa-wallet"></i><b><?= format_rupiah((int)$stat['denda']) ?></b><span>Total denda</span></article>
</section>
<section class="panel table-wrap">
    <div class="section-head">
        <div>
            <p class="eyebrow">RIWAYAT</p>
            <h2>Aktivitas peminjaman</h2>
        </div><a class="btn secondary" href="anggota.php">Kembali</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Buku</th>
                <th>Pengajuan</th>
                <th>Status</th>
                <th>Kembali</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody><?php while ($row = mysqli_fetch_assoc($history)) {
                    $badge = status_badge($row['status']); ?><tr>
                    <td class="book-table"><img src="../uploads/covers/<?= e($row['cover']) ?>" alt="">
                        <div><b><?= e($row['judul']) ?></b><small><?= e($row['kode_buku']) ?></small></div>
                    </td>
                    <td><?= format_tanggal_id($row['tanggal_pengajuan']) ?></td>
                    <td><span class="badge <?= $badge[1] ?>"><?= $badge[0] ?></span></td>
                    <td><?= format_tanggal_id($row['tanggal_kembali']) ?></td>
                    <td><?= format_rupiah((int)$row['denda']) ?></td>
                </tr><?php } ?></tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>