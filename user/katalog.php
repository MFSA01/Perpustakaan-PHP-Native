<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('user');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$id = (int) $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bid = isset($_POST['id_buku']) ? (int) $_POST['id_buku'] : 0;
    $stmt = mysqli_prepare($koneksi, "SELECT b.stok, COUNT(p.id_peminjaman) aktif FROM buku b LEFT JOIN peminjaman p ON p.id_buku=b.id_buku AND p.id_user=? AND p.status IN ('pending','dipinjam') WHERE b.id_buku=? GROUP BY b.id_buku");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $bid);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$cek || $cek['stok'] < 1 || $cek['aktif'] > 0) {
        set_flash('danger', 'Peminjaman tidak dapat diajukan.');
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO peminjaman (id_user, id_buku, tanggal_pengajuan, status) VALUES (?, ?, CURDATE(), 'pending')");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $bid);
        mysqli_stmt_execute($stmt);
        set_flash('success', 'Pengajuan peminjaman terkirim.');
    }
    header('Location: katalog.php');
    exit;
}

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kat = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;
$sql = "SELECT b.*, k.nama_kategori, COUNT(p.id_peminjaman) aktif FROM buku b JOIN kategori k ON k.id_kategori=b.id_kategori LEFT JOIN peminjaman p ON p.id_buku=b.id_buku AND p.id_user=$id AND p.status IN ('pending','dipinjam') WHERE 1=1";
$types = '';
$params = array();
if ($cari != '') { $sql .= ' AND (b.judul LIKE ? OR b.penulis LIKE ?)'; $like = "%$cari%"; $types = 'ss'; $params = array($like, $like); }
if ($kat > 0) { $sql .= ' AND b.id_kategori=?'; $types .= 'i'; $params[] = $kat; }
$sql .= ' GROUP BY b.id_buku ORDER BY b.judul';
$stmt = mysqli_prepare($koneksi, $sql);
if (count($params) > 0) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$books = mysqli_stmt_get_result($stmt);
$cats = mysqli_query($koneksi, 'SELECT * FROM kategori ORDER BY nama_kategori');
$page_title = 'Katalog Buku';
require __DIR__ . '/../includes/header.php';
?>
<section class="catalog-heading"><div><p class="eyebrow">KOLEKSI PERPUSTAKAAN</p><h2>Temukan bacaan berikutnya</h2><p>Jelajahi koleksi yang tersedia dan ajukan peminjaman dalam satu langkah.</p></div><div class="catalog-count"><i class="fa-solid fa-layer-group"></i><span><?= mysqli_num_rows($books) ?> buku ditemukan</span></div></section>
<form class="catalog-filters" method="get"><div class="catalog-search"><i class="fa-solid fa-magnifying-glass"></i><input name="cari" value="<?= e($cari) ?>" placeholder="Cari judul atau penulis"></div><select name="kategori"><option value="0">Semua kategori</option><?php while ($c = mysqli_fetch_assoc($cats)) { ?><option value="<?= $c['id_kategori'] ?>" <?= $kat == $c['id_kategori'] ? 'selected' : '' ?>><?= e($c['nama_kategori']) ?></option><?php } ?></select><button class="btn primary"><i class="fa-solid fa-sliders"></i> Terapkan</button></form>
<section class="catalog-grid"><?php while ($b = mysqli_fetch_assoc($books)) { $disable = $b['stok'] < 1 || $b['aktif'] > 0; if ($b['aktif'] > 0) $button = 'Sedang diproses'; elseif ($b['stok'] < 1) $button = 'Stok habis'; else $button = 'Ajukan peminjaman'; ?><article class="catalog-card"><div class="catalog-cover"><img src="../uploads/covers/<?= e($b['cover']) ?>" alt="Cover <?= e($b['judul']) ?>"><span><?= e($b['kondisi']) ?></span></div><div class="catalog-content"><span class="catalog-category"><?= e($b['nama_kategori']) ?></span><h3><?= e($b['judul']) ?></h3><p class="catalog-author"><i class="fa-regular fa-pen-to-square"></i> <?= e($b['penulis']) ?></p><p class="catalog-stock"><i class="fa-solid fa-cubes-stacked"></i> <?= e((string) $b['stok']) ?> eksemplar tersedia</p><a class="book-detail-link" href="detail_buku.php?id=<?= $b['id_buku'] ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat detail & sinopsis</a><form method="post"><input type="hidden" name="id_buku" value="<?= $b['id_buku'] ?>"><button class="btn primary full" <?= $disable ? 'disabled' : '' ?>><i class="fa-solid <?= $disable ? 'fa-lock' : 'fa-bookmark' ?>"></i> <?= $button ?></button></form></div></article><?php } ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
