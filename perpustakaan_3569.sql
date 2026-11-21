-- ==========================================================
-- DATABASE: perpustakaan_3569
-- BRAND   : Perpustakaann Farrel
-- ENGINE  : MariaDB / MySQL (Laragon)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS perpustakaan_3569
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE perpustakaan_3569;

-- Hapus tabel jika sudah ada (urutan anak ke induk agar aman terhadap FK)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS peminjaman;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================================
-- 1. TABEL USERS (Admin & User Biasa)
-- ==========================================================
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL COMMENT 'Enkripsi MD5',
    email VARCHAR(100) NOT NULL UNIQUE,
    no_telepon VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================================
-- 2. TABEL KATEGORI (Induk Relasi Buku)
-- ==========================================================
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(60) NOT NULL UNIQUE,
    deskripsi TEXT DEFAULT NULL
) ENGINE=InnoDB;

-- ==========================================================
-- 3. TABEL BUKU (CRUD Utama dengan 5 Input Wajib)
-- ==========================================================
CREATE TABLE buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    kode_buku VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tanggal_terbit DATE NOT NULL,
    kondisi ENUM('Baru', 'Baik', 'Cukup') NOT NULL DEFAULT 'Baik',
    stok INT NOT NULL DEFAULT 1,
    sinopsis TEXT NOT NULL,
    cover VARCHAR(255) NOT NULL DEFAULT 'default-cover.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_buku_kategori
        FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==========================================================
-- 4. TABEL PEMINJAMAN (Transaksi, ACC Admin, & Denda)
-- ==========================================================
CREATE TABLE peminjaman (
    id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_buku INT NOT NULL,
    tanggal_pengajuan DATE NOT NULL,
    tanggal_pinjam DATE DEFAULT NULL,
    tanggal_jatuh_tempo DATE DEFAULT NULL,
    tanggal_kembali DATE DEFAULT NULL,
    status ENUM('pending', 'dipinjam', 'dikembalikan', 'ditolak') NOT NULL DEFAULT 'pending',
    hari_terlambat INT NOT NULL DEFAULT 0,
    denda INT NOT NULL DEFAULT 0,
    catatan_admin VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_peminjaman_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_peminjaman_buku
        FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==========================================================
-- SEED DATA: KATEGORI (6 Data)
-- ==========================================================
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Teknologi & Pemrograman', 'Buku mengenai rekayasa perangkat lunak, pemrograman web, basis data, dan arsitektur sistem.'),
('Sistem Informasi & Bisnis', 'Referensi manajemen proyek TI, analisis proses bisnis, tata kelola, dan strategi digital.'),
('Fiksi & Sastra', 'Novel, kumpulan cerpen, dan karya sastra Indonesia maupun terjemahan.'),
('Pengembangan Diri', 'Buku produktivitas, kebiasaan kerja efektif, komunikasi, dan kepemimpinan.'),
('Sains & Matematika', 'Literatur logika komputasi, statistika, fisika populer, dan metode ilmiah.'),
('Sejarah & Biografi', 'Catatan peristiwa sejarah, tokoh dunia, serta perjalanan inovator teknologi.');

-- ==========================================================
-- SEED DATA: USERS (1 Admin Manual + 10 User Biasa = 11 Data)
-- Catatan: Akun admin hanya ditambah manual via INSERT INTO.
-- Login default wajib: username = admin, password = admin
-- Login user contoh  : password = user123 (terenkripsi MD5)
-- ==========================================================
INSERT INTO users (nama_lengkap, username, password, email, no_telepon, role) VALUES
('Administrator Perpustakaann Farrel', 'admin', MD5('admin'), 'admin@perpustakaannfarrel.local', '081200000001', 'admin'),
('Aditya Pratama', 'aditya.pratama', MD5('user123'), 'aditya.pratama@mail.com', '081234560001', 'user'),
('Nadia Kusuma', 'nadia.kusuma', MD5('user123'), 'nadia.kusuma@mail.com', '081234560002', 'user'),
('Bagas Saputra', 'bagas.saputra', MD5('user123'), 'bagas.saputra@mail.com', '081234560003', 'user'),
('Citra Maharani', 'citra.maharani', MD5('user123'), 'citra.maharani@mail.com', '081234560004', 'user'),
('Dimas Wibowo', 'dimas.wibowo', MD5('user123'), 'dimas.wibowo@mail.com', '081234560005', 'user'),
('Fika Rahmawati', 'fika.rahmawati', MD5('user123'), 'fika.rahmawati@mail.com', '081234560006', 'user'),
('Gilang Ramadhan', 'gilang.ramadhan', MD5('user123'), 'gilang.ramadhan@mail.com', '081234560007', 'user'),
('Hana Permatasari', 'hana.permata', MD5('user123'), 'hana.permata@mail.com', '081234560008', 'user'),
('Iqbal Nugroho', 'iqbal.nugroho', MD5('user123'), 'iqbal.nugroho@mail.com', '081234560009', 'user'),
('Larasati Putri', 'larasati.putri', MD5('user123'), 'larasati.putri@mail.com', '081234560010', 'user');

-- ==========================================================
-- SEED DATA: BUKU (12 Data)
-- ==========================================================
INSERT INTO buku (id_kategori, kode_buku, judul, penulis, penerbit, tanggal_terbit, kondisi, stok, sinopsis, cover) VALUES
(1, 'BK-001', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Prentice Hall', '2008-08-01', 'Baik', 5, 'Panduan praktis menulis kode yang bersih, mudah dibaca, mudah dipelihara, serta contoh refactoring nyata untuk pengembang perangkat lunak.', 'cover_bk001.jpg'),
(1, 'BK-002', 'Pemrograman Web Dinamis dengan PHP 8 dan MariaDB', 'Budi Raharjo', 'Informatika Bandung', '2024-03-15', 'Baru', 6, 'Membahas arsitektur web server-side menggunakan PHP 8 modern, integrasi basis data MariaDB, autentikasi sesi, hingga keamanan aplikasi.', 'cover_bk002.jpg'),
(1, 'BK-003', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 'O Reilly Media', '2017-03-16', 'Baik', 4, 'Mengupas fondasi sistem data modern mulai dari model relasional, indeks, replikasi, hingga keandalan dan skalabilitas aplikasi.', 'cover_bk003.jpg'),
(2, 'BK-004', 'Analisis dan Perancangan Sistem Informasi', 'Jogiyanto HM', 'Andi Publisher', '2022-06-10', 'Baik', 5, 'Referensi komprehensif mengenai siklus hidup pengembangan sistem informasi, pemodelan proses bisnis, serta studi kasus perusahaan.', 'cover_bk004.jpg'),
(2, 'BK-005', 'The Lean Startup', 'Eric Ries', 'Crown Business', '2011-09-13', 'Cukup', 3, 'Metodologi membangun produk digital secara iteratif melalui eksperimen terukur, validasi pasar, dan pembelajaran cepat.', 'cover_bk005.jpg'),
(3, 'BK-006', 'Laut Bercerita', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', '2017-10-20', 'Baik', 7, 'Novel fiksi sejarah yang mengikuti kisah persahabatan, kehilangan, dan pencarian keadilan para aktivis mahasiswa di akhir era 1990-an.', 'cover_bk006.jpg'),
(3, 'BK-007', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', '2005-05-01', 'Cukup', 4, 'Bagian pertama Tetralogi Buru yang menggambarkan pergulatan pemikiran Minke di tengah masyarakat kolonial Hindia Belanda.', 'cover_bk007.jpg'),
(4, 'BK-008', 'Atomic Habits', 'James Clear', 'Gramedia Pustaka Utama', '2019-09-16', 'Baru', 8, 'Kerangka kerja sistematis untuk membangun kebiasaan baik dan menghentikan kebiasaan buruk melalui perubahan kecil yang konsisten.', 'cover_bk008.jpg'),
(4, 'BK-009', 'Deep Work: Rules for Focused Success', 'Cal Newport', 'Grand Central Publishing', '2016-01-05', 'Baik', 5, 'Strategi melatih fokus mendalam tanpa distraksi untuk menghasilkan karya kognitif bernilai tinggi di era serba terhubung.', 'cover_bk009.jpg'),
(5, 'BK-010', 'Matematika Diskrit dan Aplikasinya pada Ilmu Komputer', 'Rinaldi Munir', 'Informatika Bandung', '2021-01-12', 'Baru', 6, 'Pembahasan logika proposisi, himpunan, relasi, graf, pohon, dan kombinatorika yang menjadi dasar struktur data dan algoritma.', 'cover_bk010.jpg'),
(5, 'BK-011', 'A Brief History of Time', 'Stephen Hawking', 'Bantam Books', '1998-09-01', 'Cukup', 2, 'Penjelasan ilmiah populer tentang asal-usul alam semesta, lubang hitam, ruang-waktu, dan mekanika kuantum untuk pembaca umum.', 'cover_bk011.jpg'),
(6, 'BK-012', 'Steve Jobs: The Exclusive Biography', 'Walter Isaacson', 'Simon & Schuster', '2011-10-24', 'Baik', 3, 'Biografi mendalam mengenai perjalanan hidup, filosofi desain, dan inovasi teknologi yang membentuk industri komputer modern.', 'cover_bk012.jpg');

-- ==========================================================
-- SEED DATA: PEMINJAMAN (5 Transaksi Contoh)
-- ==========================================================
INSERT INTO peminjaman (id_user, id_buku, tanggal_pengajuan, tanggal_pinjam, tanggal_jatuh_tempo, tanggal_kembali, status, hari_terlambat, denda, catatan_admin) VALUES
(2, 1, '2026-11-20', NULL, NULL, NULL, 'pending', 0, 0, NULL),
(3, 2, '2026-11-17', '2026-11-17', '2026-11-24', NULL, 'dipinjam', 0, 0, 'Disetujui, harap kembalikan tepat waktu.'),
(4, 6, '2026-11-08', '2026-11-08', '2026-11-15', NULL, 'dipinjam', 0, 0, 'Disetujui. Sudah melewati jatuh tempo untuk simulasi denda.'),
(5, 8, '2026-11-01', '2026-11-01', '2026-11-08', '2026-11-07', 'dikembalikan', 0, 0, 'Buku kembali dalam kondisi baik.'),
(6, 4, '2026-10-25', '2026-10-25', '2026-11-01', '2026-11-04', 'dikembalikan', 3, 6000, 'Terlambat 3 hari, denda Rp 6.000 telah dicatat.');