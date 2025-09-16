<?php
// reset_passwords.php - Script untuk Reset Hash Password di Database
// Jalankan script ini sekali di browser atau CLI untuk memperbaiki hash password.
// Setelah itu, hapus atau rename file ini untuk keamanan.

// Konfigurasi Database (sesuaikan dengan config.php Anda)
$host = 'localhost';
$dbname = 'akademik_db';
$db_username = 'root'; // Sesuaikan dengan user MySQL Anda
$db_password = ''; // Sesuaikan dengan password MySQL Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil!<br>";
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Fungsi untuk generate hash baru
function generateHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Update password untuk admin
$admin_password = 'admin123';
$admin_hash = generateHash($admin_password);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$admin_hash]);
echo "Password admin diupdate ke hash baru untuk 'admin123'. Hash: " . $admin_hash . "<br>";

// Update password untuk mahasiswa, sari, andi
$mhs_password = 'mhs123';
$mhs_hash = generateHash($mhs_password);
$usernames = ['mahasiswa', 'sari', 'andi'];
foreach ($usernames as $username) {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$mhs_hash, $username]);
    echo "Password untuk '$username' diupdate ke hash baru untuk 'mhs123'. Hash: " . $mhs_hash . "<br>";
}

echo "<br>Reset selesai! Sekarang coba login lagi dengan:<br>";
echo "- Admin: username 'admin', password 'admin123'<br>";
echo "- Mahasiswa: username 'mahasiswa' (atau 'sari'/'andi'), password 'mhs123'<br>";
echo "Setelah login berhasil, hapus file ini untuk keamanan.";
?>