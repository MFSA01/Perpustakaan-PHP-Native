<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('user');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$page_title = 'Pengaturan Akun';
$role_folder = 'user';

// Jalankan logika proses POST/Upload/Redirect sebelum mengirim HTML header
require __DIR__ . '/../includes/profile_handler.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/profile_view.php';
require __DIR__ . '/../includes/footer.php';
