<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('user');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
$page_title = 'Detail Buku';
$role_folder = 'user';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/book_detail.php';
require __DIR__ . '/../includes/footer.php';
?>
