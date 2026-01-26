<?php
session_start();

function checkAuth($roles = []) {
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    if (!empty($roles) && !in_array($_SESSION['role'], (array)$roles)) {
        // Redirect unauthorized access
        header("Location: index.php?error=unauthorized");
        exit;
    }
}

// Helper untuk mencegah XSS
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>