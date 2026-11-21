# Product Requirements Document (PRD) — Perpustakaann Farrel

## 1. Ringkasan Produk
**Perpustakaann Farrel** adalah aplikasi web sistem manajemen perpustakaan dinamis berbasis PHP 8.3 dan MariaDB (`perpustakaan_3569`) dengan dukungan autentikasi multi-role (`admin` dan `user`), pengelolaan katalog buku dan kategori berbasis relasi Primary Key–Foreign Key, alur pengajuan dan persetujuan (ACC) peminjaman buku, serta perhitungan denda keterlambatan otomatis.

## 2. Lingkungan & Batasan Teknis (WAJIB)
- **Bahasa Backend:** PHP 8.3 (kompatibel penuh dengan PHP 8.3 tanpa fungsi deprecated, tanpa Notice/Warning).
- **Database & Ekstensi:** MariaDB / MySQL menggunakan **`mysqli` prosedural** (`mysqli_connect`, `mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`, `mysqli_stmt_get_result`). Jangan gunakan PDO atau `mysqli` OOP.
- **Server Lokal:** Laragon (akses melalui `http://localhost/perpustakaann-farrel/` atau virtual host Laragon).
- **Frontend Styling:** **Vanilla CSS murni** (DILARANG memakai Bootstrap, Tailwind CSS, Bulma, atau CSS framework apa pun). JavaScript hanya vanilla JS ringan untuk UX (konfirmasi hapus, preview upload gambar, toggle modal/sidebar mobile).
- **Nama Database:** `perpustakaan_3569` (skema dan data awal mengacu pada `perpustakaan_3569.sql`).
- **Standar Kode:** Clean code, modular (pisahkan koneksi, helper function, layout header/sidebar, dan logika proses), prepared statement untuk seluruh query berparameter, dan escaping `htmlspecialchars()` untuk setiap output HTML.

## 3. Sistem Autentikasi & Hak Akses (Multi-Role)
### 3.1 Mekanisme Login & Session
- Halaman `login.php` memiliki form `username` dan `password`.
- Validasi password menggunakan **`md5($password)`** yang dicocokkan terhadap kolom `users.password`.
- Akun default wajib berjalan: `username = admin`, `password = admin` (tersimpan di tabel `users` dengan `MD5('admin')`).
- Setelah login berhasil, simpan `id_user`, `nama_lengkap`, `username`, dan `role` ke dalam `$_SESSION`:
  - Jika `role === 'admin'` -> redirect ke `admin/dashboard.php`.
  - Jika `role === 'user'` -> redirect ke `user/dashboard.php`.
- Setiap halaman terproteksi wajib memeriksa session melalui helper guard (`require_login()`, `require_role('admin')`, `require_role('user')`).
- Menu **Logout** (`logout.php`) wajib menghancurkan seluruh session (`session_unset()`, `session_destroy()`) dan melakukan redirect langsung ke `login.php`.

### 3.2 Registrasi Pengguna
- Halaman `register.php` terbuka untuk pengunjung membuat akun baru.
- Setiap pendaftaran lewat `register.php` **wajib** menyetel `role = 'user'` di sisi server (tidak boleh ada pilihan role di form).
- Validasi: `username` dan `email` harus unik, password minimal 6 karakter, konfirmasi password cocok, password disimpan dalam bentuk `md5()`.
- Akun dengan `role = 'admin'` **tidak boleh** bisa dibuat lewat web UI (hanya ditambah manual via perintah SQL `INSERT INTO`).

## 4. Fitur Fungsional

### 4.1 Modul Admin (`admin/`)
1. **Dashboard Ringkasan (`admin/dashboard.php`)**
   - Kartu statistik ringkas: Total Judul Buku, Total Eksemplar Tersedia, Pengajuan Pending (butuh ACC), Buku Sedang Dipinjam, dan Total Denda Terkumpul.
   - Tabel daftar pengajuan peminjaman terbaru berstatus `pending` dengan pintasan ACC/Tolak.
2. **CRUD Buku (`admin/buku.php`, `admin/buku_tambah.php`, `admin/buku_edit.php`, `admin/buku_hapus.php`)**
   - Menampilkan daftar buku dengan JOIN ke tabel `kategori`, dilengkapi pencarian judul/penulis/kode buku, filter kategori, dan thumbnail cover.
   - Form Tambah & Edit Buku **wajib** memuat kelima tipe input berikut:
     1. **`input type="text"`**: Kode Buku, Judul Buku, Penulis, Penerbit.
     2. **`textarea`**: Sinopsis buku.
     3. **`input type="radio"`**: Kondisi Buku (`Baru`, `Baik`, `Cukup`).
     4. **`input type="date"`**: Tanggal Terbit.
     5. **`input type="file"`**: Upload Cover Buku (validasi ekstensi `jpg`, `jpeg`, `png`, `webp`; batas ukuran maksimal 2MB; nama file di-rename unik agar tidak bentrok; disimpan di `uploads/covers/`; tampilkan preview gambar; saat edit jika tidak upload baru tetap pakai cover lama; saat hapus buku, hapus file cover fisik jika bukan `default-cover.jpg`).
   - Relasi FK: Dropdown `<select>` memilih `id_kategori` dari tabel `kategori`, plus `input type="number"` untuk Stok (minimal 0).
   - Validasi penghapusan: jika buku masih terikat data peminjaman aktif (`pending`/`dipinjam`), tampilkan flash message yang jelas dan cegah crash FK error.
3. **CRUD Kategori (`admin/kategori.php`)**
   - Tambah, tampilkan, edit, dan hapus kategori (cegah hapus jika masih digunakan oleh tabel `buku` dengan pesan informatif).
4. **Manajemen & ACC Peminjaman (`admin/peminjaman.php`)**
   - Filter status (`Semua`, `Pending`, `Dipinjam`, `Dikembalikan`, `Ditolak`).
   - **Aksi ACC (`pending` -> `dipinjam`):**
     - Cek stok buku harus lebih dari 0.
     - Set `tanggal_pinjam = CURDATE()` (hari ini) dan `tanggal_jatuh_tempo = tanggal_pinjam + 7 hari`.
     - Kurangi `buku.stok` sebesar 1 secara atomik (gunakan transaksi `mysqli_begin_transaction`).
     - Bisa menyertakan catatan admin opsional.
   - **Aksi Tolak (`pending` -> `ditolak`):**
     - Ubah status menjadi `ditolak` tanpa mengubah stok, disertai catatan alasan opsional.
   - **Aksi Pengembalian (`dipinjam` -> `dikembalikan`):**
     - Set `tanggal_kembali = hari ini`.
     - Hitung keterlambatan melalui fungsi PHP khusus: jika `tanggal_kembali` melewati `tanggal_jatuh_tempo`, hitung selisih hari (`hari_terlambat`) dan kalikan **Rp 2.000 per hari** (`denda = hari_terlambat * 2000`). Jika tepat waktu atau lebih awal, `hari_terlambat = 0` dan `denda = 0`.
     - Tambah kembali `buku.stok` sebesar 1 dalam transaksi database.
     - Tampilkan notifikasi ringkas berisi rincian keterlambatan dan total denda yang tercatat.
5. **Data Anggota (`admin/anggota.php`)**
   - Menampilkan daftar user terdaftar (hanya baca / lihat riwayat jumlah pinjaman masing-masing anggota, tanpa form pembuatan admin).

### 4.2 Modul User Biasa (`user/`)
1. **Dashboard User (`user/dashboard.php`)**
   - Ringkasan pribadi: Jumlah Pengajuan Pending, Buku Sedang Dipinjam, Riwayat Selesai, dan Total Denda.
   - Daftar buku yang sedang dipinjam beserta status sisa hari atau peringatan keterlambatan (estimasi denda berjalan jika sudah melewati jatuh tempo).
2. **Katalog Buku & Pengajuan Pinjam (`user/katalog.php`)**
   - Tampilan kartu/grid katalog buku profesional (cover, judul, penulis, kategori, kondisi, stok tersedia, tombol detail sinopsis).
   - Pencarian berdasarkan judul/penulis dan filter kategori.
   - Tombol **"Ajukan Peminjaman"**:
     - Nonaktif jika stok buku 0 atau user sudah memiliki pengajuan `pending` / sedang meminjam buku yang sama.
     - Mengirim record baru ke tabel `peminjaman` dengan `status = 'pending'` dan `tanggal_pengajuan = hari ini`.
3. **Riwayat Peminjaman & Denda (`user/riwayat.php`)**
   - Tabel riwayat lengkap semua transaksi milik user yang sedang login: kode/judul buku, tanggal pengajuan, tanggal pinjam, jatuh tempo, tanggal kembali, badge status, hari terlambat, dan nominal denda dalam format Rupiah.
   - Tombol **"Batalkan Pengajuan"** khusus untuk transaksi yang masih berstatus `pending`.

## 5. Penerapan Wajib 4 Konsep PHP
Seluruh 4 konsep PHP berikut **harus diterapkan secara eksplisit dan fungsional** (terutama pada modul form CRUD buku & helper):
1. **Array:** Mendefinisikan opsi kondisi buku (`$daftar_kondisi = ['Baru', 'Baik', 'Cukup']`), daftar ekstensi file cover yang diizinkan (`$allowed_ext`), array `$errors = []` untuk menampung hasil validasi form, dan mapping badge status peminjaman.
2. **Perulangan (`foreach` / `while`):** Me-render radio button kondisi buku dari array `$daftar_kondisi`, mengisi `<option>` kategori dan baris tabel dari hasil query `mysqli_fetch_assoc()`, serta menampilkan daftar pesan validasi form.
3. **Percabangan (`if/else` / `switch`):** Validasi input form & upload file, pengecekan role session, logika perhitungan denda keterlambatan lebih dari 7 hari, serta penentuan label/warna badge status.
4. **Function:** Membuat fungsi modular terstruktur di `includes/functions.php`, antara lain:
   - `hitung_denda(string $tanggal_jatuh_tempo, string $tanggal_kembali): array` (mengembalikan `['hari_terlambat' => int, 'denda' => int]`)
   - `upload_cover_buku(array $file, ?string $cover_lama = null): array`
   - `validasi_form_buku(array $post): array`
   - `format_rupiah(int $nominal): string`
   - `format_tanggal_id(?string $tanggal): string`
   - `set_flash(string $type, string $message): void` & `get_flash(): ?array`

## 6. Design System & Arahan Visual (Anti AI-Slop)
### 6.1 Filosofi Visual
- **Gaya:** Dark Mode Elegan, editorial, rapi, tenang, dan profesional seperti aplikasi katalog/arsip modern — **bukan** tampilan generik "AI slop".
- **Larangan Keras Visual:**
  - DILARANG menggunakan gradasi warna mencolok (tidak ada linear-gradient merah-neon, ungu, atau tombol bergradasi). Gunakan **warna solid yang presisi** dan border tipis (`1px solid`).
  - DILARANG menggunakan efek glow/neon shadow, glassmorphism blur berlebihan, atau emoji sebagai pengganti ikon UI. Gunakan SVG ikon minimalis 16–20px dengan garis konsisten.
  - DILARANG menumpuk kartu dengan animasi memantul/berlebihan. Transisi cukup `150ms ease` pada perubahan warna/border.

### 6.2 Palet Warna (CSS Custom Properties)
```css
:root {
  /* Permukaan (Hitam Arang Lembut — bukan #000000 pekat) */
  --bg-canvas: #0E0E10;
  --bg-sidebar: #141417;
  --bg-surface: #1A1A1F;
  --bg-surface-elevated: #222228;
  --bg-input: #121215;

  /* Border Halus */
  --border-subtle: #2A2A32;
  --border-strong: #3A3A45;

  /* Aksen Utama: Merah Maroon Elegan */
  --maroon-primary: #8B1E2D;
  --maroon-hover: #A42436;
  --maroon-muted: rgba(139, 30, 45, 0.16);
  --maroon-border: #6E1723;

  /* Tipografi (Kontras Nyaman Dibaca) */
  --text-primary: #F2EFEA;
  --text-secondary: #B4B0AC;
  --text-muted: #7E7A77;

  /* Status Semantik Redup (Harmonis dengan Dark Mode) */
  --status-success-bg: rgba(46, 125, 82, 0.16);
  --status-success-text: #7ED3A5;
  --status-warning-bg: rgba(180, 130, 42, 0.16);
  --status-warning-text: #E9C478;
  --status-danger-bg: rgba(178, 48, 62, 0.18);
  --status-danger-text: #F28B96;
  --status-info-bg: rgba(74, 122, 168, 0.16);
  --status-info-text: #94BEE8;
}