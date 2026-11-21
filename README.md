# Perpustakaan Farrel 📚

Sistem informasi manajemen perpustakaan dinamis berbasis PHP Native dan MariaDB. Dibuat untuk tugas Ujian Tengah Semester (UTS) mata kuliah Pemrograman Web. Aplikasi mendukung autentikasi multi-role, katalog buku berelasi, alur persetujuan (ACC) peminjaman buku oleh admin, serta penghitungan denda otomatis.

---

## 📌 Fitur Utama

### 1. Multi-Role & Autentikasi
- **Admin**: Akses penuh ke dashboard analitik, manajemen buku & kategori, persetujuan peminjaman, dan pengawasan anggota.
- **User / Anggota**: Akses katalog buku, pengajuan peminjaman mandiri, pemantauan status persetujuan, dan riwayat transaksi.
- Autentikasi sesi aman (`$_SESSION`) dengan enkripsi password MD5.
- Proteksi route via middleware function (`require_login()`, `require_role()`).

### 2. Panel Admin
- **Dashboard Ringkasan**: Statistik total judul buku, eksemplar tersedia, pengajuan pending, buku dipinjam, dan total denda terkumpul.
- **CRUD Buku Terstruktur**: Form lengkap (kode buku, judul, penulis, penerbit, sinopsis, kondisi buku, tanggal terbit, stok, serta upload cover buku).
- **CRUD Kategori**: Relasi Foreign Key aman dengan proteksi penghapusan saat data berelasi masih aktif.
- **Approval Workflow Peminjaman**:
  - ACC pengajuan peminjaman (otomatis memotong stok buku secara atomik).
  - Tolak pengajuan peminjaman disertai catatan alasan.
  - Proses pengembalian buku (stok buku otomatis dikembalikan).
  - Perhitungan denda otomatis (Rp 2.000/hari saat melewati 7 hari masa pinjam).
- **Data Anggota**: Monitoring user terdaftar dan riwayat aktivitas peminjaman setiap anggota.

### 3. Panel User
- **Katalog Buku Interaktif**: Live search judul/penulis, filter berdasarkan kategori, informasi ketersediaan stok, dan modal sinopsis buku.
- **Pengajuan Peminjaman**: Validasi stok real-time dan proteksi anti-duplikasi pengajuan.
- **Riwayat & Monitoring Status**: Lacak status persetujuan (`pending`, `dipinjam`, `dikembalikan`, `ditolak`), tenggat waktu jatuh tempo, serta rincian denda.
- **Batal Pengajuan**: Pembatalan pengajuan mandiri selama status masih `pending`.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.3 (Native procedural via `mysqli`, Prepared Statements)
- **Database**: MariaDB / MySQL (`perpustakaan_3569`)
- **Frontend**: Vanilla HTML5, Vanilla CSS murni (Dark mode editorial, tanpa external framework CSS), Vanilla JavaScript
- **Local Server**: Laragon / XAMPP

---

## 🗂️ Struktur Direktori

```text
uts/
├── admin/               # Modul halaman & aksi admin (CRUD buku, kategori, approval)
├── assets/              # Asset statis (CSS, JS, SVG icons/logo)
├── config/              # File konfigurasi koneksi database
├── includes/            # Helper global, autentikasi middleware, header & footer partials
├── screenshots/         # Asset tangkapan layar tampilan aplikasi
├── uploads/             # Direktori upload cover buku & avatar user
│   ├── avatars/
│   └── covers/
├── user/                # Modul halaman user biasa (katalog, pengajuan, riwayat)
├── index.php            # Landing page / routing utama
├── login.php            # Halaman login multi-role
├── register.php         # Pendaftaran akun anggota baru
├── logout.php           # Pembersihan sesi user
├── perpustakaan_3569.sql # Skema database & data awal (seed data)
└── README.md
```

---

## 🚀 Panduan Instalasi

### 1. Clone Repositori
Pindahkan direktori kerja ke web root server lokal:
- **Laragon**: `C:\laragon\www\`
- **XAMPP**: `C:\xampp\htdocs\`

Clone repositori ini:
```bash
git clone https://github.com/MFSA01/Perpustakaan-PHP-Native.git uts
```

### 2. Setup Database
1. Buka database management tool (HeidiSQL, phpMyAdmin, atau MySQL CLI).
2. Buat database baru bernama `perpustakaan_3569` (atau jalankan script SQL langsung):
   ```sql
   CREATE DATABASE IF NOT EXISTS perpustakaan_3569;
   ```
3. Import file `perpustakaan_3569.sql` ke dalam database:
   ```bash
   mysql -u root -p perpustakaan_3569 < perpustakaan_3569.sql
   ```

### 3. Konfigurasi Koneksi
Pastikan kredensial di `config/database.php` sudah sesuai dengan environment server lokal Anda:
```php
$koneksi = mysqli_connect('localhost', 'root', '', 'perpustakaan_3569');
```

### 4. Jalankan Aplikasi
Buka browser dan akses aplikasi melalui URL:
- **Laragon**: `http://localhost/uts` atau `http://uts.test` (jika menggunakan Pretty URLs Laragon)
- **XAMPP**: `http://localhost/uts`

---

## 🔑 Kredensial Akun Default

| Role | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin` | Akses penuh dashboard administrator |
| **User** | `aditya.pratama` | `user123` | Contoh akun anggota terdaftar |

> **Catatan:** Akun anggota baru juga dapat didaftarkan langsung melalui menu [Registrasi](http://localhost/uts/register.php). Akun admin hanya dibuat secara manual melalui query SQL pada tabel `users`.

---

## 📸 Tangkapan Layar (Screenshots)

*Simpan gambar tangkapan layar di folder `screenshots/` dengan nama file yang sesuai di bawah ini:*

### Autentikasi
| Login Multi-Role | Registrasi Pengguna |
| :---: | :---: |
| ![Halaman Login](screenshots/login.png) | ![Halaman Registrasi](screenshots/register.png) |

### Panel Admin
| Dashboard Ringkasan | Manajemen & CRUD Buku |
| :---: | :---: |
| ![Dashboard Admin](screenshots/admin-dashboard.png) | ![CRUD Buku](screenshots/admin-buku.png) |

| Approval Peminjaman | Manajemen Anggota |
| :---: | :---: |
| ![Approval Peminjaman](screenshots/admin-peminjaman.png) | ![Data Anggota](screenshots/admin-anggota.png) |

### Panel User
| Katalog Buku | Riwayat & Denda |
| :---: | :---: |
| ![Katalog Buku](screenshots/user-katalog.png) | ![Riwayat Peminjaman](screenshots/user-riwayat.png) |

---

## 👨‍💻 Pengembang

- **Nama**: Farrel
- **GitHub**: [@MFSA01](https://github.com/MFSA01)
