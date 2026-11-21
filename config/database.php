<?php



$koneksi = mysqli_connect('localhost', 'root', '', 'perpustakaan_3569');
if ($koneksi === false) {
    exit('Koneksi database gagal. Pastikan Laragon dan database perpustakaan_3569 aktif.');
}
mysqli_set_charset($koneksi, 'utf8mb4');
