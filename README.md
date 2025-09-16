Sistem Akademik - Proyek PHP
Nama: Wyandhanu Maulidan NugrahaNIM: 24151109Kelas: 2C  
Deskripsi Proyek
Proyek ini adalah aplikasi web berbasis PHP untuk sistem akademik yang memungkinkan:

Login: Autentikasi pengguna (Admin dan Mahasiswa) menggunakan username dan password.
Navigasi Dinamis: Menu berbeda berdasarkan role (Admin: Kelola Courses/Students, Mahasiswa: Daftar Courses).
Manajemen Courses:
Admin: Create, Read, Update, Delete (CRUD) courses.
Mahasiswa: Melihat daftar courses aktif dan enroll ke course.


Manajemen Students: Admin dapat melakukan CRUD pada data mahasiswa.
Keamanan: Proteksi halaman menggunakan session untuk memastikan akses sesuai role.
Database: Menggunakan MySQL dengan struktur tabel yang diberikan (akademik_db.sql).

Struktur File
Berikut adalah file-file utama dalam proyek ini:

akademik_db.sql: Skrip SQL untuk membuat database akademik_db, tabel (users, students, courses, takes), dan data sample.
config.php: Konfigurasi koneksi database dan fungsi utilitas (proteksi halaman, navigasi dinamis).
login.php: Form login dengan autentikasi menggunakan password_verify().
logout.php: Menghapus session dan redirect ke halaman login.
index.php: Dashboard utama dengan navigasi dinamis berdasarkan role.
courses.php: Halaman untuk:
Admin: Kelola courses (tambah, edit, hapus).
Mahasiswa: Melihat daftar courses dan enroll.


students.php: Halaman untuk Admin mengelola data mahasiswa (tambah, edit, hapus).
reset_passwords.php: Script sementara untuk reset hash password di database jika login gagal.

Prasyarat

PHP: Versi 7.x atau 8.x dengan ekstensi pdo_mysql diaktifkan.
MySQL: Server MySQL/MariaDB yang berjalan.
Web Server: Apache/Nginx dengan PHP terkonfigurasi.
Browser: Untuk mengakses aplikasi web.

Cara Instalasi

Setup Database:

Buat database di MySQL:mysql -u root -p
CREATE DATABASE akademik_db;


Import file akademik_db.sql:mysql -u root -p akademik_db < akademik_db.sql


Pastikan data tabel (users, students, courses, takes) terisi dengan benar.


Konfigurasi Koneksi Database:

Edit config.php untuk menyesuaikan kredensial MySQL:$host = 'localhost';
$dbname = 'akademik_db';
$username = 'root'; // Ganti sesuai user MySQL Anda
$password = ''; // Ganti sesuai password MySQL Anda




Setup File Proyek:

Simpan semua file PHP (config.php, login.php, logout.php, index.php, courses.php, students.php) di folder proyek (misalnya, /var/www/html/akademik).
Pastikan folder proyek dapat diakses oleh web server.


Jalankan Aplikasi:

Akses http://localhost/akademik/login.php melalui browser.
Gunakan kredensial berikut untuk login:
Admin: username: admin, password: admin123
Mahasiswa: username: mahasiswa (atau sari/andi), password: mhs123





Mengatasi Masalah Login
Jika Anda mendapatkan pesan "Username atau password salah!" meskipun memasukkan kredensial yang benar, kemungkinan hash password di database tidak cocok. Ikuti langkah berikut:

Reset Hash Password:

Simpan file reset_passwords.php di folder proyek:<?php
// reset_passwords.php
$host = 'localhost';
$dbname = 'akademik_db';
$db_username = 'root'; // Sesuaikan
$db_password = ''; // Sesuaikan
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil!<br>";
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
function generateHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
$admin_password = 'admin123';
$admin_hash = generateHash($admin_password);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$admin_hash]);
echo "Password admin diupdate ke hash baru untuk 'admin123'.<br>";
$mhs_password = 'mhs123';
$mhs_hash = generateHash($mhs_password);
$usernames = ['mahasiswa', 'sari', 'andi'];
foreach ($usernames as $username) {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$mhs_hash, $username]);
    echo "Password untuk '$username' diupdate ke hash baru untuk 'mhs123'.<br>";
}
echo "<br>Reset selesai! Coba login lagi.<br>";
?>


Akses http://localhost/akademik/reset_passwords.php di browser.
Script akan memperbarui hash password di tabel users.
Hapus file ini setelah digunakan untuk keamanan.


Verifikasi Data di Database:

Jalankan query berikut di MySQL untuk memeriksa data users:SELECT username, password, role FROM users;


Pastikan username admin, mahasiswa, sari, andi ada dengan hash password yang valid.


Periksa Koneksi Database:

Jika koneksi gagal, periksa kredensial di config.php dan pastikan MySQL berjalan.
Uji koneksi dengan script:<?php
require_once 'config.php';
try {
    $pdo->query("SELECT 1");
    echo "Koneksi berhasil!";
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
?>




Debug Login:

Tambahkan debug di login.php untuk melihat detail error:if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    $stmt = $pdo->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    var_dump($user);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Username atau password salah!';
        if (!$user) {
            $error .= ' (Username tidak ditemukan)';
        } else {
            $error .= ' (Password tidak cocok)';
        }
    }
}


Periksa output untuk melihat apakah username ditemukan atau password cocok.



Uji Coba Aplikasi
Sebagai Admin

Login dengan admin / admin123.
Akses Dashboard (index.php): Lihat menu navigasi (Dashboard, Kelola Courses, Kelola Students, Logout).
Kelola Courses (courses.php):
Tambah course baru (contoh: code TIF999, nama Test Course, credits 3, semester 1).
Edit course yang ada (ubah nama atau status).
Hapus course (konfirmasi dengan dialog).


Kelola Students (students.php):
Tambah student baru (contoh: username newmhs, NIM 230004, full name Nama Baru).
Edit data student (ubah email atau status).
Hapus student (cascade ke tabel takes).


Logout dan verifikasi redirect ke login.php.

Sebagai Mahasiswa

Login dengan mahasiswa / mhs123 (atau sari / mhs123, andi / mhs123).
Akses Dashboard (index.php): Lihat menu navigasi (Dashboard, Daftar Courses, Logout).
Daftar Courses (courses.php):
Lihat daftar courses aktif.
Enroll ke course (contoh: TIF101).
Coba enroll ulang course yang sama (harus muncul pesan "Sudah enrolled!").
Lihat daftar enrolled courses di bagian bawah halaman.


Logout dan verifikasi redirect ke login.php.

Screenshot Uji Coba (Deskripsi)

Login: Form login sederhana dengan pesan error jika gagal.
Dashboard Admin: Menampilkan menu navigasi dan pesan selamat datang untuk Admin.
Courses Admin: Tabel courses dengan form tambah/edit/hapus.
Courses Mahasiswa: Tabel courses aktif dengan tombol enroll dan daftar enrolled courses.
Students Admin: Tabel students dengan form tambah/edit/hapus.

Catatan

Keamanan: File reset_passwords.php harus dihapus setelah digunakan.
Peningkatan: Form edit inline di courses.php dan students.php bisa diubah ke modal dengan JavaScript untuk UX lebih baik.
Limitasi: Grade dan status di tabel takes belum diimplementasikan (bisa ditambahkan).
Debug: Jika login masih gagal setelah reset, periksa output debug di login.php atau query SELECT * FROM users; untuk memastikan data benar.

Kontribusi
Proyek ini dibuat oleh Wyandhanu Maulidan Nugraha (NIM: 24151109, Kelas: 2C) sebagai bagian dari tugas akademik.
