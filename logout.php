<?php
// logout.php
session_start();
session_destroy();
header("Location: index.php"); // Pastikan index.php satu folder dengan file ini
exit;
?>