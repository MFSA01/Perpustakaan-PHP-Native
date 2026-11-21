<?php

// Session menyimpan data pengguna yang sudah login.
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

function base_url($path = '')
{
    if (defined('APP_ROOT')) {
        return APP_ROOT . $path;
    }

    return $path;
}

function require_login()
{
    if (!isset($_SESSION['id_user'])) {
        header('Location: ' . base_url('/login.php'));
        exit;
    }
}
function require_role($role)
{
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        header('Location: ' . base_url('/index.php'));
        exit;
    }
}
