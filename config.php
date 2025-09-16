<?php
// config.php - Database Configuration
$host = 'localhost';
$dbname = 'akademik_db';
$username = 'root'; // Ganti sesuai setup MySQL Anda
$password = ''; // Ganti sesuai setup MySQL Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fungsi untuk proteksi halaman
function requireLogin($pdo) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    // Ambil role user
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $role = $stmt->fetchColumn();
    $_SESSION['role'] = $role;
    return $role;
}

// Fungsi untuk navigasi dinamis
function getNavigation($role) {
    $nav = '<nav><ul>';
    if ($role === 'Admin') {
        $nav .= '<li><a href="index.php">Dashboard</a></li>
                 <li><a href="courses.php">Kelola Courses</a></li>
                 <li><a href="students.php">Kelola Students</a></li>';
    } else { // Mahasiswa
        $nav .= '<li><a href="index.php">Dashboard</a></li>
                 <li><a href="courses.php">Daftar Courses</a></li>';
    }
    $nav .= '<li><a href="logout.php">Logout</a></li>';
    $nav .= '</ul></nav>';
    return $nav;
}
?>