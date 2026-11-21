<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$page_title = 'Kelola Kategori Buku';

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
        if ($used) {
            set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh koleksi buku.');
        } else {
            $stmt = mysqli_prepare($koneksi, 'DELETE FROM kategori WHERE id_kategori=?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            set_flash('success', 'Kategori berhasil dihapus.');
        }
    } elseif ($nama !== '') {
        if ($aksi === 'edit') {
            $stmt = mysqli_prepare($koneksi, 'UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id_kategori=?');
            mysqli_stmt_bind_param($stmt, 'ssi', $nama, $deskripsi, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, 'INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)');
            mysqli_stmt_bind_param($stmt, 'ss', $nama, $deskripsi);
        }
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Kategori berhasil disimpan.');
        } else {
            set_flash('danger', 'Nama kategori sudah digunakan.');
        }
    }
    header('Location: kategori.php');
    exit;
}

$list = mysqli_query($koneksi, 'SELECT k.*, COUNT(b.id_buku) jumlah FROM kategori k LEFT JOIN buku b ON b.id_kategori=k.id_kategori GROUP BY k.id_kategori ORDER BY k.nama_kategori');
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <div>
        <p class="eyebrow">INVENTARIS</p>
        <h2>Kategori Buku</h2>
    </div>
</div>

<section class="split category-split-layout">
    <!-- Form Tambah Kategori -->
    <form class="panel modern-form-card" method="post">
        <div class="form-header-bar">
            <div class="form-header-icon"><i class="fa-solid fa-tags"></i></div>
            <div>
                <h3>Tambah Kategori Baru</h3>
                <p class="form-header-sub">Klasifikasikan koleksi buku agar mudah ditelusuri.</p>
            </div>
        </div>

        <input type="hidden" name="aksi" value="tambah">
        
        <label>Nama Kategori <span class="required-star">*</span>
            <input type="text" name="nama_kategori" placeholder="Contoh: Kecerdasan Buatan" required>
        </label>
        
        <label>Deskripsi Singkat
            <textarea name="deskripsi" rows="4" placeholder="Jelaskan cakupan topik kategori ini..."></textarea>
        </label>
        
        <div class="form-action-footer">
            <button class="btn primary" type="submit"><i class="fa-solid fa-plus"></i> Simpan Kategori</button>
        </div>
    </form>

    <!-- Tabel Daftar Kategori -->
    <section class="panel table-wrap">
        <div class="table-card-head">
            <h3>Daftar Kategori Terdaftar</h3>
            <span class="badge info"><i class="fa-solid fa-layer-group"></i> Total <?= mysqli_num_rows($list) ?></span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama & Deskripsi Kategori</th>
                    <th>Jumlah Buku</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($k = mysqli_fetch_assoc($list)): ?>
                    <tr>
                        <td>
                            <b class="category-name-text"><?= e($k['nama_kategori']) ?></b>
                            <p class="category-desc-text"><?= e($k['deskripsi'] ?: 'Belum ada deskripsi.') ?></p>
                        </td>
                        <td>
                            <span class="badge secondary">
                                <i class="fa-solid fa-book"></i> <?= (int)$k['jumlah'] ?> koleksi
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_kategori" value="<?= $k['id_kategori'] ?>">
                                <button class="btn btn-sm danger" data-confirm="Hapus kategori '<?= e($k['nama_kategori']) ?>'? Buku berkategori ini harus dipindahkan terlebih dahulu.">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
