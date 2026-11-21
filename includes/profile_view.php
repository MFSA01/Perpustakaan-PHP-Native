<div class="section-head">
    <div>
        <p class="eyebrow">PENGATURAN AKUN</p>
        <h2>Profil & Keamanan Pengguna</h2>
    </div>
    <a class="btn secondary" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
</div>

<?php foreach ($errors as $err): ?>
    <div class="alert danger"><i class="fa-solid fa-circle-exclamation"></i> <?= e($err) ?></div>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data" class="profile-layout">
    <!-- Kolom Kiri: Kartu Identitas & Foto Profil -->
    <article class="panel profile-side-card">
        <div class="profile-avatar-wrap">
            <?php if ($avatarPath): ?>
                <img id="avatarPreview" class="profile-large-avatar" src="<?= $avatarPath ?>" alt="Foto Profil">
            <?php else: ?>
                <div id="avatarPlaceholder" class="profile-large-avatar profile-avatar-init">
                    <?= e(strtoupper(substr($user['nama_lengkap'], 0, 1))) ?>
                </div>
                <img id="avatarPreview" class="profile-large-avatar" style="display:none;" alt="Foto Profil">
            <?php endif; ?>
            <label class="avatar-upload-btn" title="Ganti Foto">
                <i class="fa-solid fa-camera"></i>
                <input type="file" name="foto_profil" accept=".jpg,.jpeg,.png,.webp" id="avatarInput" style="display:none;">
            </label>
        </div>

        <h3 class="profile-card-name"><?= e($user['nama_lengkap']) ?></h3>
        <p class="profile-card-user">@<?= e($user['username']) ?></p>
        <span class="badge <?= $user['role'] === 'admin' ? 'danger' : 'info' ?>">
            <i class="fa-solid <?= $user['role'] === 'admin' ? 'fa-shield-halved' : 'fa-user' ?>"></i>
            <?= ucfirst(e($user['role'])) ?> Perpustakaan
        </span>

        <div class="profile-meta-rows">
            <div>
                <small>Terdaftar Sejak</small>
                <b><?= format_tanggal_id($user['created_at']) ?></b>
            </div>
            <div>
                <small>ID Anggota</small>
                <b>#<?= str_pad((string)$user['id_user'], 4, '0', STR_PAD_LEFT) ?></b>
            </div>
        </div>
        <p class="avatar-help-text"><i class="fa-solid fa-circle-info"></i> JPG, PNG, WEBP (Maksimal 2MB).</p>
    </article>

    <!-- Kolom Kanan: Form Data Pribadi & Ganti Password -->
    <div class="profile-main-stack">
        <!-- Bagian 1: Data Pribadi -->
        <article class="panel form-stack">
            <div class="profile-section-title">
                <i class="fa-regular fa-id-card"></i>
                <div>
                    <h3>Informasi Pribadi</h3>
                    <small class="text-muted">Perbarui nama lengkap dan data kontak akun Anda.</small>
                </div>
            </div>

            <div class="form-grid">
                <label>Nama Lengkap
                    <input type="text" name="nama_lengkap" value="<?= e($user['nama_lengkap']) ?>" required>
                </label>
                <label>Username
                    <input type="text" value="<?= e($user['username']) ?>" disabled class="input-disabled" title="Username tidak dapat diubah">
                </label>
            </div>

            <div class="form-grid">
                <label>Alamat Email
                    <input type="email" name="email" value="<?= e($user['email']) ?>" required>
                </label>
                <label>Nomor Telepon / WhatsApp
                    <input type="text" name="no_telepon" value="<?= e($user['no_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </label>
            </div>
        </article>

        <!-- Bagian 2: Keamanan & Ganti Sandi -->
        <article class="panel form-stack">
            <div class="profile-section-title">
                <i class="fa-solid fa-lock"></i>
                <div>
                    <h3>Ganti Kata Sandi</h3>
                    <small class="text-muted">Kosongkan kolom sandi jika Anda tidak ingin mengubahnya.</small>
                </div>
            </div>

            <label>Kata Sandi Saat Ini
                <span class="password-field">
                    <input type="password" name="password_lama" id="pass_lama" placeholder="Masukkan sandi lama jika ingin mengganti">
                    <button class="password-toggle" type="button" data-password-toggle="pass_lama" aria-label="Tampilkan sandi" title="Tampilkan sandi">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </span>
            </label>

            <div class="form-grid">
                <label>Kata Sandi Baru
                    <span class="password-field">
                        <input type="password" name="password_baru" id="pass_baru" placeholder="Minimal 6 karakter">
                        <button class="password-toggle" type="button" data-password-toggle="pass_baru" aria-label="Tampilkan sandi" title="Tampilkan sandi">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </span>
                </label>
                <label>Konfirmasi Kata Sandi Baru
                    <span class="password-field">
                        <input type="password" name="konfirmasi_password" id="pass_konfirmasi" placeholder="Ulangi sandi baru">
                        <button class="password-toggle" type="button" data-password-toggle="pass_konfirmasi" aria-label="Tampilkan sandi" title="Tampilkan sandi">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </span>
                </label>
            </div>

            <div class="profile-save-action">
                <button type="submit" class="btn primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </div>
        </article>
    </div>
</form>

<script>
// Preview Foto Profil Langsung Saat Dipilih
document.getElementById('avatarInput')?.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const preview = document.getElementById('avatarPreview');
        const placeholder = document.getElementById('avatarPlaceholder');
        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
        if (placeholder) {
            placeholder.style.display = 'none';
        }
    }
});
</script>
