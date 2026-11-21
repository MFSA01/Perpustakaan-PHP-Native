<?php
if (!isset($page_title)) $page_title = 'Perpustakaann Farrel';
$root = defined('APP_ROOT') ? APP_ROOT : '';
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$menu = $isAdmin ? [
    ['dashboard.php', 'Ringkasan', 'fa-house'],
    ['buku.php', 'Koleksi Buku', 'fa-book'],
    ['kategori.php', 'Kategori', 'fa-tags'],
    ['peminjaman.php', 'Peminjaman', 'fa-clock-rotate-left'],
    ['anggota.php', 'Anggota', 'fa-users'],
    ['tampilan_user.php', 'Tampilan User', 'fa-glasses'],
    ['profil.php', 'Pengaturan Akun', 'fa-gear']
] : [
    ['dashboard.php', 'Ringkasan', 'fa-house'],
    ['katalog.php', 'Katalog Buku', 'fa-book'],
    ['riwayat.php', 'Riwayat', 'fa-clock-rotate-left'],
    ['profil.php', 'Pengaturan Akun', 'fa-gear']
];

// Periksa apakah user memiliki foto profil
$userFoto = $_SESSION['foto_profil'] ?? null;
$avatarSrc = (!empty($userFoto) && $userFoto !== 'default-avatar.png' && is_file(dirname(__DIR__) . '/uploads/avatars/' . $userFoto))
    ? $root . '/uploads/avatars/' . e($userFoto)
    : null;
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($page_title) ?> · Perpustakaann Farrel</title>
    <link rel="icon" type="image/svg+xml" href="<?= $root ?>/assets/img/logo.svg">
    <link rel="stylesheet" href="<?= $root ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="<?= $root ?>/index.php">
                <span class="brand-mark-svg"><img src="<?= $root ?>/assets/img/logo.svg" alt="PF Logo" width="34" height="34"></span>
                <span>Perpustakaann<small>FARREL</small></span>
            </a>
            <p class="menu-title">MENU UTAMA</p>
            <nav>
                <?php foreach ($menu as [$file, $label, $icon]): ?>
                    <a href="<?= $root ?>/<?= $isAdmin ? 'admin/' : 'user/' ?><?= $file ?>" class="<?= basename($_SERVER['PHP_SELF']) === $file ? 'active' : '' ?>">
                        <i class="fa-solid <?= $icon ?>"></i>
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="sidebar-reading">
                <i class="fa-solid fa-book-bookmark"></i>
                <b>Ruang baca digital</b>
                <small>Koleksi untuk setiap rasa ingin tahu.</small>
            </div>
            <div class="sidebar-foot">
                <div class="sidebar-user-info">
                    <?php if ($avatarSrc): ?>
                        <img class="sidebar-user-avatar" src="<?= $avatarSrc ?>" alt="Avatar">
                    <?php else: ?>
                        <span class="sidebar-user-init"><?= e(strtoupper(substr($_SESSION['nama_lengkap'] ?? 'U', 0, 1))) ?></span>
                    <?php endif; ?>
                    <div>
                        <span><?= e($_SESSION['nama_lengkap'] ?? '') ?></span>
                        <small><?= e(ucfirst($_SESSION['role'] ?? '')) ?></small>
                    </div>
                </div>
                <div class="sidebar-actions-foot">
                    <a class="settings-link" href="<?= $root ?>/<?= $isAdmin ? 'admin/' : 'user/' ?>profil.php" title="Pengaturan Akun">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    <a class="logout-link" href="<?= $root ?>/logout.php" title="Keluar">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                    </a>
                </div>
            </div>
        </aside>

        <main class="main">
            <header class="topbar">
                <button class="menu-toggle" data-sidebar-toggle aria-label="Buka menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="top-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" placeholder="Cari judul, kategori atau penulis...">
                </div>
                <div class="top-actions">
                    <button aria-label="Notifikasi" title="Notifikasi Sistem">
                        <i class="fa-regular fa-bell"></i>
                    </button>
                    <a href="<?= $root ?>/<?= $isAdmin ? 'admin/' : 'user/' ?>profil.php" class="top-profile-link" title="Pengaturan Akun">
                        <?php if ($avatarSrc): ?>
                            <img class="profile-dot-img" src="<?= $avatarSrc ?>" alt="Avatar">
                        <?php else: ?>
                            <span class="profile-dot"><?= e(strtoupper(substr($_SESSION['nama_lengkap'] ?? 'U', 0, 1))) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </header>

            <?php if ($flash = get_flash()): ?>
                <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
