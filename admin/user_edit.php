<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = mysqli_prepare($koneksi, 'SELECT * FROM users WHERE id_user=?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user || $user['role'] === 'admin') {
    set_flash('danger', 'Data anggota tidak ditemukan.');
    header('Location: anggota.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp = trim($_POST['no_telepon'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($nama === '' || $username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Lengkapi seluruh data dengan format yang valid.';
    }

    if (empty($errors)) {
        if ($pass !== '') {
            if (strlen($pass) < 6) {
                $errors[] = 'Password baru minimal 6 karakter.';
            } else {
                $hash = md5($pass);
                $stmt = mysqli_prepare($koneksi, 'UPDATE users SET nama_lengkap=?, username=?, email=?, no_telepon=?, password=? WHERE id_user=?');
                mysqli_stmt_bind_param($stmt, 'sssssi', $nama, $username, $email, $telp, $hash, $id);
            }
        } else {
            $stmt = mysqli_prepare($koneksi, 'UPDATE users SET nama_lengkap=?, username=?, email=?, no_telepon=? WHERE id_user=?');
            mysqli_stmt_bind_param($stmt, 'ssssi', $nama, $username, $email, $telp, $id);
        }

        if (empty($errors)) {
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Data anggota berhasil diperbarui.');
                header('Location: user_detail.php?id=' . $id);
                exit;
            }
            $errors[] = 'Username atau email sudah digunakan oleh akun lain.';
        }
    }
    $user = array_merge($user, $_POST);
}

$page_title = 'Edit Data Anggota';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <div>
        <p class="eyebrow">KEANGGOTAAN</p>
        <h2>Edit Data Anggota</h2>
    </div>
    <a class="btn secondary" href="user_detail.php?id=<?= $id ?>"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert danger"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div>
<?php endforeach; ?>

<form class="panel modern-form-card member-form" method="post">
    <div class="form-header-bar">
        <div class="form-header-icon"><i class="fa-solid fa-user-pen"></i></div>
        <div>
            <h3>Perbarui Informasi Anggota</h3>
            <p class="form-header-sub">ID Anggota: <b>#<?= str_pad((string)$user['id_user'], 4, '0', STR_PAD_LEFT) ?></b></p>
        </div>
    </div>

    <div class="form-grid">
        <label>Nama Lengkap <span class="required-star">*</span>
            <input type="text" name="nama_lengkap" value="<?= e($user['nama_lengkap']) ?>" required>
        </label>
        <label>Username <span class="required-star">*</span>
            <input type="text" name="username" value="<?= e($user['username']) ?>" required>
        </label>
        <label>Alamat Email <span class="required-star">*</span>
            <input type="email" name="email" value="<?= e($user['email']) ?>" required>
        </label>
        <label>Nomor Telepon / WhatsApp
            <input type="text" name="no_telepon" value="<?= e($user['no_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
        </label>
    </div>

    <label class="full-width">Ganti Kata Sandi (Opsional)
        <span class="password-field">
            <input type="password" name="password" id="pass_edit_member" placeholder="Biarkan kosong jika tidak ingin mengubah sandi">
            <button class="password-toggle" type="button" data-password-toggle="pass_edit_member" aria-label="Tampilkan sandi" title="Tampilkan sandi">
                <i class="fa-solid fa-eye"></i>
            </button>
        </span>
    </label>

    <div class="form-action-footer">
        <a class="btn secondary" href="user_detail.php?id=<?= $id ?>"><i class="fa-solid fa-xmark"></i> Batal</a>
        <button class="btn primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>