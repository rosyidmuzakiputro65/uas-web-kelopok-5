<?php
session_start();

// 1️⃣ koneksi MongoDB
require "config.php"; // PASTIKAN di sini ada $users

// 2️⃣ ambil data dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// 3️⃣ cari user
$user = $users->findOne(['username' => $username]);

// 4️⃣ verifikasi
if ($user && password_verify($password, $user['password'])) {

    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    // 5️⃣ redirect berdasarkan role
    switch ($user['role']) {
        case 'admin':
            header("Location: admin.php");
            break;
        case 'muhaffizh':
            header("Location: muhaffizh.php");
            break;
        case 'dosen':
            header("Location: dosen.php");
            break;
        default:
            echo "Role tidak dikenal";
    }
    exit;

} else {
    echo "<script>
        alert('Username atau Password salah');
        window.location='index.php';
    </script>";
    exit;
}
