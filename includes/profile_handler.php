<?php
// Murni Logika Proses Profil & Upload (Dijalankan sebelum HTML Output)
$idUser = (int) $_SESSION['id_user'];

// Ambil data user terkini dari database
$stmtUser = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmtUser, 'i', $idUser);
mysqli_stmt_execute($stmtUser);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser));

if (!$user) {
    set_flash('danger', 'Akun tidak ditemukan.');
    header('Location: ../login.php');
    exit;
}

$errors = [];

// Handle Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp = trim($_POST['no_telepon'] ?? '');
    $passLama = $_POST['password_lama'] ?? '';
    $passBaru = $_POST['password_baru'] ?? '';
    $passKonfirmasi = $_POST['konfirmasi_password'] ?? '';

    // 1. Validasi Input Dasar
    if ($nama === '' || $email === '') {
        $errors[] = 'Nama lengkap dan email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }

    // 2. Cek Keunikan Email (jika diubah)
    if (empty($errors) && $email !== $user['email']) {
        $stmtCek = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ? AND id_user != ?");
        mysqli_stmt_bind_param($stmtCek, 'si', $email, $idUser);
        mysqli_stmt_execute($stmtCek);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCek)) > 0) {
            $errors[] = 'Email sudah digunakan oleh akun lain.';
        }
        mysqli_stmt_close($stmtCek);
    }

    // 3. Upload & Ganti Foto Profil
    $fotoProfil = !empty($user['foto_profil']) ? $user['foto_profil'] : 'default-avatar.png';
    if (!empty($_FILES['foto_profil']['name']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto_profil'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Foto profil harus berupa file JPG, JPEG, PNG, atau WEBP.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran file foto profil maksimal 2MB.';
        } elseif (@getimagesize($file['tmp_name']) === false) {
            $errors[] = 'File yang diunggah bukan gambar valid.';
        } else {
            $namaFotoBaru = 'avatar_' . $idUser . '_' . time() . '.' . $ext;
            $destPath = dirname(__DIR__) . '/uploads/avatars/' . $namaFotoBaru;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                // Hapus foto lama jika bukan default
                if ($fotoProfil !== 'default-avatar.png' && is_file(dirname(__DIR__) . '/uploads/avatars/' . $fotoProfil)) {
                    @unlink(dirname(__DIR__) . '/uploads/avatars/' . $fotoProfil);
                }
                $fotoProfil = $namaFotoBaru;
            } else {
                $errors[] = 'Gagal menyimpan foto profil ke server.';
            }
        }
    }

    // 4. Validasi Ganti Password (jika diisi)
    $updatePassword = false;
    $newHash = '';
    if (!empty($passBaru) || !empty($passLama) || !empty($passKonfirmasi)) {
        if (empty($passLama)) {
            $errors[] = 'Password saat ini harus diisi untuk mengubah password.';
        } elseif (md5($passLama) !== $user['password']) {
            $errors[] = 'Password saat ini tidak sesuai.';
        } elseif (strlen($passBaru) < 6) {
            $errors[] = 'Password baru minimal terdiri dari 6 karakter.';
        } elseif ($passBaru !== $passKonfirmasi) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        } else {
            $updatePassword = true;
            $newHash = md5($passBaru);
        }
    }

    // 5. Eksekusi Update ke Database jika lolos validasi
    if (empty($errors)) {
        if ($updatePassword) {
            $stmtUp = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap = ?, email = ?, no_telepon = ?, foto_profil = ?, password = ? WHERE id_user = ?");
            mysqli_stmt_bind_param($stmtUp, 'sssssi', $nama, $email, $telp, $fotoProfil, $newHash, $idUser);
        } else {
            $stmtUp = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap = ?, email = ?, no_telepon = ?, foto_profil = ? WHERE id_user = ?");
            mysqli_stmt_bind_param($stmtUp, 'ssssi', $nama, $email, $telp, $fotoProfil, $idUser);
        }

        if (mysqli_stmt_execute($stmtUp)) {
            // Perbarui Session agar langsung tampil di Topbar/Sidebar
            $_SESSION['nama_lengkap'] = $nama;
            $_SESSION['foto_profil'] = $fotoProfil;

            set_flash('success', 'Pengaturan profil Anda berhasil disimpan.');
            header('Location: profil.php');
            exit;
        } else {
            $errors[] = 'Terjadi kesalahan sistem saat memperbarui profil.';
        }
        mysqli_stmt_close($stmtUp);
    }
}

// Fallback avatar path
$avatarPath = (!empty($user['foto_profil']) && is_file(dirname(__DIR__) . '/uploads/avatars/' . $user['foto_profil']))
    ? '../uploads/avatars/' . e($user['foto_profil'])
    : null;
