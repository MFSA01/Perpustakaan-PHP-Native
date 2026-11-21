<form method="post" enctype="multipart/form-data" class="panel modern-form-card book-form">
    <div class="form-header-bar">
        <div class="form-header-icon"><i class="fa-solid fa-book"></i></div>
        <div>
            <h3>Informasi Bibliografi & Inventaris</h3>
            <p class="form-header-sub">Pastikan data katalog akurat untuk memudahkan pencarian anggota.</p>
        </div>
    </div>

    <div class="form-grid">
        <label>Kode Buku <span class="required-star">*</span>
            <input type="text" name="kode_buku" placeholder="Contoh: BK-001" value="<?= e($data['kode_buku'] ?? '') ?>" required>
        </label>
        <label>Judul Buku <span class="required-star">*</span>
            <input type="text" name="judul" placeholder="Masukkan judul buku lengkap" value="<?= e($data['judul'] ?? '') ?>" required>
        </label>
        <label>Penulis <span class="required-star">*</span>
            <input type="text" name="penulis" placeholder="Nama penulis / pengarang" value="<?= e($data['penulis'] ?? '') ?>" required>
        </label>
        <label>Penerbit <span class="required-star">*</span>
            <input type="text" name="penerbit" placeholder="Nama penerbit buku" value="<?= e($data['penerbit'] ?? '') ?>" required>
        </label>
        <label>Tanggal Terbit <span class="required-star">*</span>
            <input type="date" name="tanggal_terbit" value="<?= e($data['tanggal_terbit'] ?? '') ?>" required>
        </label>
        <label>Kategori Buku <span class="required-star">*</span>
            <select name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?= (int)$k['id_kategori'] ?>" <?= ((int)($data['id_kategori'] ?? 0) === (int)$k['id_kategori']) ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>
        <label>Jumlah Stok <span class="required-star">*</span>
            <input type="number" name="stok" min="0" placeholder="0" value="<?= e((string)($data['stok'] ?? 0)) ?>" required>
        </label>
        <div class="form-group-custom">
            <span class="custom-label">Kondisi Buku <span class="required-star">*</span></span>
            <div class="segmented-control">
                <?php $daftar_kondisi = ['Baru', 'Baik', 'Cukup'];
                foreach ($daftar_kondisi as $kondisi): ?>
                    <label class="segmented-item <?= ($data['kondisi'] ?? 'Baik') === $kondisi ? 'active' : '' ?>">
                        <input type="radio" name="kondisi" value="<?= $kondisi ?>" <?= ($data['kondisi'] ?? 'Baik') === $kondisi ? 'checked' : '' ?>>
                        <span><?= $kondisi ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <label class="full-width">Sinopsis Cerita / Ringkasan Buku <span class="required-star">*</span>
        <textarea name="sinopsis" rows="5" placeholder="Tuliskan ulasan ringkas mengenai alur atau konten buku..." required><?= e($data['sinopsis'] ?? '') ?></textarea>
    </label>

    <div class="cover-drop-card">
        <div class="cover-thumb-box">
            <img id="coverPreview" class="cover-thumb-img" src="../uploads/covers/<?= e($data['cover'] ?? 'default-cover.jpg') ?>" alt="Preview Cover">
        </div>
        <div class="cover-drop-info">
            <div class="cover-drop-title">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <b>Sampul Buku (Cover)</b>
            </div>
            <p class="cover-drop-hint">Format didukung: JPG, JPEG, PNG, WEBP. Ukuran maksimal berkas 2MB.</p>
            <label class="btn-file-upload">
                <i class="fa-solid fa-image"></i> Pilih Berkas Cover
                <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp" data-cover-preview="#coverPreview">
            </label>
            <span id="coverFileName" class="file-chosen-name">Belum ada file baru dipilih</span>
        </div>
    </div>

    <div class="form-action-footer">
        <a class="btn secondary" href="buku.php"><i class="fa-solid fa-xmark"></i> Batal</a>
        <button class="btn primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Buku</button>
    </div>
</form>
