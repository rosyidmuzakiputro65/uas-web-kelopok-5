<?php
// Matikan pesan Warning agar tampilan bersih
error_reporting(0);

require __DIR__ . '/vendor/autoload.php';

session_start();

try {
    // Koneksi ke MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->tahfizh_db;
    
    // Definisi Collection
    $users = $db->users;
    $santri = $db->santri;
    $setoran = $db->setoran;

} catch (Exception $e) {
    die("Koneksi Database Gagal. Pastikan MongoDB sudah start.");
}

// Fungsi Cek Login & Role
function checkAuth($role) {
    if (!isset($_SESSION['username'])) {
        header("Location: index.php"); exit;
    }
    if ($_SESSION['role'] !== $role) {
        // Redirect jika salah kamar
        header("Location: " . $_SESSION['role'] . ".php"); 
        exit;
    }
}
?>