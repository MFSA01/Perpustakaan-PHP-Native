<?php

function e($value)
{
    if ($value == null) {
        $value = '';
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function format_rupiah($nominal)
{
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}
function format_tanggal_id($tanggal)
{
    if (!$tanggal) return '-';
    $waktu = strtotime($tanggal);
    return $waktu === false ? '-' : date('d M Y', $waktu);
}
function set_flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
function hitung_denda($jatuhTempo, $kembali)
{
    $selisih = (int) floor((strtotime($kembali) - strtotime($jatuhTempo)) / 86400);
    $hari = max(0, $selisih);
    return ['hari_terlambat' => $hari, 'denda' => $hari * 2000];
}
function status_badge($status)
{
    $map = ['pending' => ['Menunggu', 'warning'], 'dipinjam' => ['Dipinjam', 'info'], 'dikembalikan' => ['Dikembalikan', 'success'], 'ditolak' => ['Ditolak', 'danger']];
    if (isset($map[$status])) {
        return $map[$status];
    }
    return ['Tidak diketahui', 'muted'];
}
function validasi_form_buku(array $post): array
{
    $errors = [];
    $wajib = ['kode_buku' => 'Kode buku', 'judul' => 'Judul', 'penulis' => 'Penulis', 'penerbit' => 'Penerbit', 'tanggal_terbit' => 'Tanggal terbit', 'id_kategori' => 'Kategori', 'sinopsis' => 'Sinopsis'];
    foreach ($wajib as $key => $label) if (trim((string)($post[$key] ?? '')) === '') $errors[] = $label . ' wajib diisi.';
    if (!in_array($post['kondisi'] ?? '', ['Baru', 'Baik', 'Cukup'], true)) $errors[] = 'Kondisi buku tidak valid.';
    if (filter_var($post['stok'] ?? null, FILTER_VALIDATE_INT) === false || (int)$post['stok'] < 0) $errors[] = 'Stok harus bilangan minimal 0.';
    return $errors;
}
function upload_cover_buku(array $file, ?string $coverLama = null): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return ['ok' => true, 'filename' => $coverLama ?? 'default-cover.jpg'];
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
    if ($error !== UPLOAD_ERR_OK) return ['ok' => false, 'message' => 'Upload cover gagal.'];
    if (!in_array($ext, $allowed_ext, true)) return ['ok' => false, 'message' => 'Cover harus JPG, JPEG, PNG, atau WEBP.'];
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) return ['ok' => false, 'message' => 'Ukuran cover maksimal 2MB.'];
    if (@getimagesize((string)$file['tmp_name']) === false) return ['ok' => false, 'message' => 'File cover bukan gambar valid.'];
    $nama = uniqid('cover_', true) . '.' . $ext;
    $tujuan = dirname(__DIR__) . '/uploads/covers/' . $nama;
    if (!move_uploaded_file((string)$file['tmp_name'], $tujuan)) return ['ok' => false, 'message' => 'Cover tidak dapat disimpan.'];
    if ($coverLama && $coverLama !== 'default-cover.jpg' && is_file(dirname(__DIR__) . '/uploads/covers/' . $coverLama)) unlink(dirname(__DIR__) . '/uploads/covers/' . $coverLama);
    return ['ok' => true, 'filename' => $nama];
}
function query_count(mysqli $koneksi, string $sql): int
{
    $hasil = mysqli_query($koneksi, $sql);
    $baris = $hasil ? mysqli_fetch_row($hasil) : [0];
    return (int)$baris[0];
}
