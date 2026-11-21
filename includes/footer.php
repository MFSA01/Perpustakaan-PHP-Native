</main>
</div>

<!-- Footer Proporsional & Profesional -->
<footer class="app-footer">
    <div class="footer-container">
        <!-- Kolom 1: Profil & Identitas Perpustakaan -->
        <div class="footer-col footer-brand-col">
            <div class="footer-brand">
                <span class="brand-mark-svg"><img src="<?= defined('APP_ROOT') ? APP_ROOT : '' ?>/assets/img/logo.svg" alt="PF Logo" width="36" height="36"></span>
                <div>
                    <b>Perpustakaann Farrel</b>
                    <small>SISTEM SIRKULASI & LITERASI DIGITAL</small>
                </div>
            </div>
            <p class="footer-desc">
                Portal manajemen literatur akademik dan koleksi pustaka terpadu. Menghadirkan akses bacaan bermutu, pelacakan sirkulasi real-time, serta kenyamanan riset bagi seluruh civitas.
            </p>
            <div class="footer-badge-wrap">
                <span class="footer-pill"><i class="fa-solid fa-shield-halved"></i> MariaDB & PHP 8.3</span>
                <span class="footer-pill"><i class="fa-solid fa-clock"></i> Buka 08:00 - 20:00 WIB</span>
            </div>
        </div>

        <!-- Kolom 2: Navigasi & Layanan Pustaka -->
        <div class="footer-col">
            <h4>Layanan & Sirkulasi</h4>
            <ul class="footer-links">
                <li><a href="<?= defined('APP_ROOT') ? APP_ROOT : '' ?>/user/katalog.php"><i class="fa-solid fa-angle-right"></i> Katalog Koleksi Buku</a></li>
                <li><a href="<?= defined('APP_ROOT') ? APP_ROOT : '' ?>/user/riwayat.php"><i class="fa-solid fa-angle-right"></i> Status Peminjaman & Denda</a></li>
                <li><a href="<?= defined('APP_ROOT') ? APP_ROOT : '' ?>/<?= ($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'user' ?>/profil.php"><i class="fa-solid fa-angle-right"></i> Pengaturan Akun & Profil</a></li>
                <li><a href="#rules"><i class="fa-solid fa-angle-right"></i> Kebijakan Pengembalian 7 Hari</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Kontak & Pusat Bantuan -->
        <div class="footer-col">
            <h4>Pusat Bantuan & Lokasi</h4>
            <ul class="footer-contact-list">
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Gedung Perpustakaan Pusat Lt. 2, Kampus AMIKOM, Yogyakarta</span>
                </li>
                <li>
                    <i class="fa-solid fa-envelope"></i>
                    <span>layanan@perpustakaannfarrel.ac.id</span>
                </li>
                <li>
                    <i class="fa-solid fa-phone"></i>
                    <span>+62 (021) 123-4567 / Helpdesk</span>
                </li>
            </ul>
            <div class="footer-socials">
                <a href="#" aria-label="Portal"><i class="fa-solid fa-globe"></i></a>
                <a href="#" aria-label="Github"><i class="fa-brands fa-github"></i></a>
                <a href="#" aria-label="Email"><i class="fa-solid fa-paper-plane"></i></a>
            </div>
        </div>
    </div>

    <!-- Baris Copyright & Status Sistem -->
    <div class="footer-bottom">
        <div class="footer-bottom-container">
            <p>&copy; <?= date('Y') ?> <b>Perpustakaann Farrel</b>. Seluruh hak cipta dilindungi undang-undang.</p>
            <div class="footer-system-status">
                <span class="status-indicator"></span>
                <span>Sistem Operasional Normal · DB: perpustakaan_3569</span>
            </div>
        </div>
    </div>
</footer>

<script src="<?= defined('APP_ROOT') ? APP_ROOT : '' ?>/assets/js/app.js"></script>
</body>
</html>
