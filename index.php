<?php
// index.php - Dashboard Utama
session_start();
require_once 'config.php';

$role = requireLogin($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Akademik</title>
    <style>body { font-family: Arial; } ul { list-style: none; } li { display: inline; margin: 10px; }</style>
</head>
<body>
    <h1>Selamat Datang, <?php echo $_SESSION['role']; ?>!</h1>
    <?php echo getNavigation($role); ?>
    <p>Ini adalah dashboard utama. Gunakan menu di atas untuk navigasi.</p>
</body>
</html>