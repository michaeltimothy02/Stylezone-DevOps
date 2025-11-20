<?php
session_start(); // Mulai session

// Hapus semua session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login atau home
header("Location: ./index.php");
exit();
?>
