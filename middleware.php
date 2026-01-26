<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth($allowedRoles) {
    // Ubah jadi array jika inputnya cuma satu string
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }

    // 1. Cek apakah sudah login
    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        header("Location: index.php");
        exit;
    }

    // 2. Cek apakah role sesuai
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        // Redirect jika salah kamar
        switch ($_SESSION['role']) {
            case 'admin': header("Location: admin.php"); break;
            case 'muhaffizh': header("Location: muhaffizh.php"); break;
            case 'dosen': header("Location: dosen.php"); break;
            default: header("Location: logout.php");
        }
        exit;
    }
}
?>