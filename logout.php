<?php

define('APP_ROOT', '');
require __DIR__ . '/includes/auth.php';
session_unset();
session_destroy();
header('Location: login.php');
exit;