<?php

define('APP_ROOT', '');
require __DIR__ . '/includes/auth.php';
if (empty($_SESSION['id_user'])) {
    header('Location: login.php');
} elseif ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
} else {
    header('Location: user/dashboard.php');
}
exit;