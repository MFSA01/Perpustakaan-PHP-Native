<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$errors = [];
$data = ['nama_lengkap' => '', 'username' => '', 'email' => '', 'no_telepon' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp = trim($_POST['no_telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $data = compact('nama_lengkap', 'username', 'email', 'no_telepon');
    $data['nama_lengkap'] = $nama;
    $data['username'] = $username;
    $data['email'] = $email;
    $data['no_telepon'] = $telp;

    if ($nama === '' || $username === '' || $email === '' || strlen($password) < 6) {
        $errors[] = 'Lengkapi seluruh data wajib. Password minimal 6 karakter.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }
    if (empty($errors)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, username, password, email, no_telepon, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $hash = md5($password);
        mysqli_stmt_bind_param($stmt, 'sssss', $nama, $username, $hash, $email, $telp);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Anggota perpustakaan berhasil ditambahkan.');
            header('Location: anggota.php');
            exit;
        }
        $errors[] = 'Username atau email sudah digunakan oleh akun lain.';
    }
}

$page_title = 'Tambah Anggota Baru';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <div>
        <p class="eyebrow">KEANGGOTAAN</p>
        <h2>Tambah Anggota Baru</h2>
    </div>
    <a class="btn secondary" href="anggota.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<?php foreach ($errors as $error): ?>
    <div class="alert danger"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div>
<?php endforeach; ?>

<form class="panel modern-form-card member-form" method="post">
    <div class="form-header-bar">
        <div class="form-header-icon"><i class="fa-solid fa-user-plus"></i></div>
        <div>
            <h3>Form Registrasi Anggota</h3>
            <p class="form-header-sub">Isi data identitas anggota perpustakaan yang akan didaftarkan.</p>
        </div>
    </div>

    <div class="form-grid">
        <label>Nama Lengkap <span class="required-star">*</span>
            <input type="text" name="nama_lengkap" placeholder="Nama lengkap anggota" value="<?= e($data['nama_lengkap']) ?>" required>
        </label>
        <label>Username <span class="required-star">*</span>
            <input type="text" name="username" placeholder="Username login" value="<?= e($data['username']) ?>" required>
        </label>
        <label>Alamat Email <span class="required-star">*</span>
            <input type="email" name="email" placeholder="contoh@email.com" value="<?= e($data['email']) ?>" required>
        </label>
        <label>Nomor Telepon / WhatsApp
            <input type="text" name="no_telepon" placeholder="08xxxxxxxxxx" value="<?= e($data['no_telepon']) ?>">
        </label>
    </div>

    <label class="full-width">Kata Sandi Awal <span class="required-star">*</span>
        <span class="password-field">
            <input type="password" name="password" id="pass_member" placeholder="Minimal 6 karakter" required>
            <button class="password-toggle" type="button" data-password-toggle="pass_member" aria-label="Tampilkan sandi" title="Tampilkan sandi">
                <i class="fa-solid fa-eye"></i>
            </button>
        </span>
    </label>

    <div class="form-action-footer">
        <a class="btn secondary" href="anggota.php"><i class="fa-solid fa-xmark"></i> Batal</a>
        <button class="btn primary" type="submit"><i class="fa-solid fa-user-check"></i> Daftarkan Anggota</button>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>