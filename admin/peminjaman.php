<?php


define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    mysqli_begin_transaction($koneksi);
    try {
        $stmt = mysqli_prepare($koneksi, 'SELECT id_buku,status FROM peminjaman WHERE id_peminjaman=? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$p) throw new Exception('Data tidak ditemukan.');
        if ($aksi === 'acc' && $p['status'] === 'pending') {
            $stmt = mysqli_prepare($koneksi, 'UPDATE buku SET stok=stok-1 WHERE id_buku=? AND stok>0');
            mysqli_stmt_bind_param($stmt, 'i', $p['id_buku']);
            mysqli_stmt_execute($stmt);
            if (!mysqli_stmt_affected_rows($stmt)) throw new Exception('Stok buku habis.');
            $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status='dipinjam',tanggal_pinjam=CURDATE(),tanggal_jatuh_tempo=DATE_ADD(CURDATE(),INTERVAL 7 DAY),catatan_admin=? WHERE id_peminjaman=?");
            mysqli_stmt_bind_param($stmt, 'si', $catatan, $id);
            mysqli_stmt_execute($stmt);
            $message = 'Peminjaman disetujui.';
        } elseif ($aksi === 'tolak' && $p['status'] === 'pending') {
            $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status='ditolak',catatan_admin=? WHERE id_peminjaman=?");
            mysqli_stmt_bind_param($stmt, 'si', $catatan, $id);
            mysqli_stmt_execute($stmt);
            $message = 'Pengajuan ditolak.';
        } elseif ($aksi === 'kembali' && $p['status'] === 'dipinjam') {
            $r = mysqli_query($koneksi, "SELECT tanggal_jatuh_tempo FROM peminjaman WHERE id_peminjaman=$id");
            $tempo = mysqli_fetch_assoc($r)['tanggal_jatuh_tempo'];
            $d = hitung_denda($tempo, date('Y-m-d'));
            $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status='dikembalikan',tanggal_kembali=CURDATE(),hari_terlambat=?,denda=?,catatan_admin=? WHERE id_peminjaman=?");
            mysqli_stmt_bind_param($stmt, 'iisi', $d['hari_terlambat'], $d['denda'], $catatan, $id);
            mysqli_stmt_execute($stmt);
            $stmt = mysqli_prepare($koneksi, 'UPDATE buku SET stok=stok+1 WHERE id_buku=?');
            mysqli_stmt_bind_param($stmt, 'i', $p['id_buku']);
            mysqli_stmt_execute($stmt);
            $message = 'Pengembalian diproses. Terlambat ' . $d['hari_terlambat'] . ' hari, denda ' . format_rupiah($d['denda']) . '.';
        } else throw new Exception('Aksi tidak valid.');
        mysqli_commit($koneksi);
        set_flash('success', $message);
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        set_flash('danger', $e->getMessage());
    }
    header('Location: peminjaman.php');
    exit;
}
$status = $_GET['status'] ?? '';
$valid = ['pending', 'dipinjam', 'dikembalikan', 'ditolak'];
$sql = 'SELECT p.*,u.nama_lengkap,b.judul FROM peminjaman p JOIN users u ON u.id_user=p.id_user JOIN buku b ON b.id_buku=p.id_buku';
if (in_array($status, $valid, true)) {
    $stmt = mysqli_prepare($koneksi, 'SELECT p.*,u.nama_lengkap,b.judul FROM peminjaman p JOIN users u ON u.id_user=p.id_user JOIN buku b ON b.id_buku=p.id_buku WHERE p.status=? ORDER BY p.created_at DESC');
    mysqli_stmt_bind_param($stmt, 's', $status);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
} else $rows = mysqli_query($koneksi, $sql . ' ORDER BY p.created_at DESC');
$page_title = 'Manajemen Peminjaman';
require __DIR__ . '/../includes/header.php'; ?><div class="filters status-filter"><a href="peminjaman.php">Semua</a><?php foreach ($valid as $s): ?><a href="?status=<?= $s ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= ucfirst($s) ?></a><?php endforeach; ?></div>
<section class="panel table-wrap">
    <table>
        <thead>
            <tr>
                <th>Anggota / Buku</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody><?php while ($r = mysqli_fetch_assoc($rows)): $badge = status_badge($r['status']); ?><tr>
                    <td><b><?= e($r['nama_lengkap']) ?></b><small><?= e($r['judul']) ?></small></td>
                    <td><span class="badge <?= $badge[1] ?>"><?= $badge[0] ?></span></td>
                    <td><?= format_tanggal_id($r['tanggal_pengajuan']) ?></td>
                    <td><?= e($r['catatan_admin']) ?></td>
                    <td>
                        <?php if (in_array($r['status'], ['pending', 'dipinjam'], true)): ?>
                            <form class="inline-form" method="post">
                                <input type="hidden" name="id" value="<?= $r['id_peminjaman'] ?>">
                                <input name="catatan" placeholder="Catatan opsional">
                                <?php if ($r['status'] === 'pending'): ?><button name="aksi" value="acc" class="btn compact primary">ACC</button><button name="aksi" value="tolak" class="btn compact danger">Tolak</button>
                                <?php else: ?><button name="aksi" value="kembali" class="btn compact secondary">Kembalikan</button>
                                <?php endif; ?>
                            </form><?php endif; ?>
                    </td>
                </tr><?php endwhile; ?></tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php';
