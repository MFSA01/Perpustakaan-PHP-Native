<form method="post" enctype="multipart/form-data" class="panel book-form">
    <div class="form-grid">
        <label>Kode buku<input type="text" name="kode_buku" value="<?= e($data['kode_buku'] ?? '') ?>" required></label>
        <label>Judul buku<input type="text" name="judul" value="<?= e($data['judul'] ?? '') ?>" required></label>
        <label>Penulis<input type="text" name="penulis" value="<?= e($data['penulis'] ?? '') ?>" required></label>
        <label>Penerbit<input type="text" name="penerbit" value="<?= e($data['penerbit'] ?? '') ?>" required></label>
        <label>Tanggal terbit<input type="date" name="tanggal_terbit" value="<?= e($data['tanggal_terbit'] ?? '') ?>" required></label>
        <label>Kategori<select name="id_kategori" required>
                <option value="">Pilih kategori</option>
                <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?= $k['id_kategori'] ?>" <?= ((int)($data['id_kategori'] ?? 0) === $k['id_kategori']) ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?></option><?php endwhile; ?>
            </select>
        </label>
        <label>Stok<input type="number" name="stok" min="0" value="<?= e((string)($data['stok'] ?? 0)) ?>" required></label>
        <fieldset>
            <legend>Kondisi buku</legend>
            <?php $daftar_kondisi = ['Baru', 'Baik', 'Cukup'];
            foreach ($daftar_kondisi as $kondisi): ?>
                <label class="radio">
                    <input type="radio" name="kondisi" value="<?= $kondisi ?>" <?= ($data['kondisi'] ?? 'Baik') === $kondisi ? 'checked' : '' ?>><?= $kondisi ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
    </div>
    <label>Sinopsis<textarea name="sinopsis" rows="6" required><?= e($data['sinopsis'] ?? '') ?></textarea></label>
    <div class="cover-upload">
        <img id="coverPreview" class="cover-preview" src="../uploads/covers/<?= e($data['cover'] ?? 'default-cover.jpg') ?>" alt="Preview cover">
        <label>Upload cover<input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp" data-cover-preview="#coverPreview">
        <small>JPG, JPEG, PNG, WEBP. Maks. 2MB.</small></label>
    </div><button class="btn primary" type="submit">Simpan buku</button>
</form>