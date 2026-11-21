<?php


define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$page_title = 'Kategori';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id_kategori'] ?? 0);
    $nama = trim($_POST['nama_kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($aksi === 'hapus') {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) total FROM buku WHERE id_kategori=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $used = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
        if ($used) set_flash('danger', 'Kategori masih dipakai buku.');
        else {
            $stmt = mysqli_prepare($koneksi, 'DELETE FROM kategori WHERE id_kategori=?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            set_flash('success', 'Kategori dihapus.');
        }
    } elseif ($nama !== '') {
        if ($aksi === 'edit') {
            $stmt = mysqli_prepare($koneksi, 'UPDATE kategori SET nama_kategori=?,deskripsi=? WHERE id_kategori=?');
            mysqli_stmt_bind_param($stmt, 'ssi', $nama, $deskripsi, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, 'INSERT INTO kategori (nama_kategori,deskripsi) VALUES (?,?)');
            mysqli_stmt_bind_param($stmt, 'ss', $nama, $deskripsi);
        }
        if (mysqli_stmt_execute($stmt)) set_flash('success', 'Kategori disimpan.');
        else set_flash('danger', 'Nama kategori sudah digunakan.');
    }
    header('Location: kategori.php');
    exit;
}
$list = mysqli_query($koneksi, 'SELECT k.*,COUNT(b.id_buku) jumlah FROM kategori k LEFT JOIN buku b ON b.id_kategori=k.id_kategori GROUP BY k.id_kategori ORDER BY k.nama_kategori');
require __DIR__ . '/../includes/header.php'; ?><section class="split">
    <form class="panel form-stack" method="post">
        <h2>Tambah kategori</h2><input type="hidden" name="aksi" value="tambah"><label>Nama kategori<input name="nama_kategori" required></label><label>Deskripsi<textarea name="deskripsi" rows="5"></textarea></label><button class="btn primary">Simpan</button>
    </form>
    <section class="panel table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Buku</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php while ($k = mysqli_fetch_assoc($list)): ?><tr>
                        <td><b><?= e($k['nama_kategori']) ?></b><small><?= e($k['deskripsi']) ?></small></td>
                        <td><?= $k['jumlah'] ?></td>
                        <td>
                            <form method="post"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id_kategori" value="<?= $k['id_kategori'] ?>"><button class="danger-link" data-confirm="Hapus kategori ini?">Hapus</button></form>
                        </td>
                    </tr><?php endwhile; ?></tbody>
        </table>
    </section>
</section><?php require __DIR__ . '/../includes/footer.php';
