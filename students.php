<?php
// students.php - Kelola Students (Hanya Admin)
session_start();
require_once 'config.php';

$role = requireLogin($pdo);

if ($role !== 'Admin') {
    header('Location: index.php');
    exit();
}

// CRUD untuk Students (Tambah, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student'])) {
        // Untuk tambah student, perlu user dulu, tapi untuk simplicity, kita tambah user dan student sekaligus
        // Asumsi password default 'mhs123' hashed
        $hashed_pw = password_hash('mhs123', PASSWORD_DEFAULT);
        $username = $_POST['username'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $nim = $_POST['nim'];
        $entry_year = $_POST['entry_year'];
        $major = $_POST['major'];
        $status = $_POST['status'];

        $pdo->beginTransaction();
        try {
            // Insert user
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name, email, phone) VALUES (?, ?, 'Mahasiswa', ?, ?, ?)");
            $stmt->execute([$username, $hashed_pw, $full_name, $email, $phone]);
            $user_id = $pdo->lastInsertId();

            // Insert student
            $stmt = $pdo->prepare("INSERT INTO students (student_id, nim, entry_year, major, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $nim, $entry_year, $major, $status]);
            $pdo->commit();
            $message = 'Student ditambahkan!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['edit_student'])) {
        $user_id = $_POST['user_id'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $nim = $_POST['nim'];
        $entry_year = $_POST['entry_year'];
        $major = $_POST['major'];
        $status = $_POST['status'];

        $pdo->beginTransaction();
        try {
            // Update user
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=? WHERE user_id=?");
            $stmt->execute([$full_name, $email, $phone, $user_id]);

            // Update student
            $stmt = $pdo->prepare("UPDATE students SET nim=?, entry_year=?, major=?, status=? WHERE student_id=?");
            $stmt->execute([$nim, $entry_year, $major, $status, $user_id]);
            $pdo->commit();
            $message = 'Student diupdate!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['delete_student'])) {
        $user_id = $_POST['user_id'];
        $pdo->beginTransaction();
        try {
            // Delete student (cascade to takes)
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id=?");
            $stmt->execute([$user_id]);
            // Delete user (cascade)
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id=?");
            $stmt->execute([$user_id]);
            $pdo->commit();
            $message = 'Student dihapus!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
        }
    }
}

// Ambil daftar students dengan join
$stmt = $pdo->query("SELECT u.user_id, u.username, u.full_name, u.email, u.phone, s.nim, s.entry_year, s.major, s.status 
                     FROM users u JOIN students s ON u.user_id = s.student_id WHERE u.role='Mahasiswa' ORDER BY s.nim");
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Students</title>
    <style>body { font-family: Arial; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; }</style>
</head>
<body>
    <?php echo getNavigation($role); ?>
    <h1>Kelola Students</h1>
    <?php if (isset($message)): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>

    <!-- Form Tambah Student -->
    <h2>Tambah Student</h2>
    <form method="POST">
        <input type="hidden" name="add_student" value="1">
        Username: <input type="text" name="username" required><br>
        Full Name: <input type="text" name="full_name" required><br>
        Email: <input type="email" name="email" required><br>
        Phone: <input type="text" name="phone"><br>
        NIM: <input type="text" name="nim" required><br>
        Entry Year: <input type="number" name="entry_year" min="2000" max="2025" required><br>
        Major: <input type="text" name="major" required><br>
        Status: <select name="status"><option>Aktif</option><option>Tidak Aktif</option><option>Lulus</option><option>Drop Out</option></select><br>
        <button type="submit">Tambah</button>
    </form>

    <!-- Daftar Students dengan Edit/Delete -->
    <table>
        <tr><th>Username</th><th>NIM</th><th>Full Name</th><th>Email</th><th>Major</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo $student['username']; ?></td>
                <td><?php echo $student['nim']; ?></td>
                <td><?php echo $student['full_name']; ?></td>
                <td><?php echo $student['email']; ?></td>
                <td><?php echo $student['major']; ?></td>
                <td><?php echo $student['status']; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                        <input type="hidden" name="edit_student" value="1">
                        Full Name: <input type="text" name="full_name" value="<?php echo $student['full_name']; ?>"><br>
                        Email: <input type="email" name="email" value="<?php echo $student['email']; ?>"><br>
                        NIM: <input type="text" name="nim" value="<?php echo $student['nim']; ?>"><br>
                        Major: <input type="text" name="major" value="<?php echo $student['major']; ?>"><br>
                        Status: <select name="status"><option <?php echo $student['status']=='Aktif'?'selected':'';?>>Aktif</option><option <?php echo $student['status']=='Tidak Aktif'?'selected':'';?>>Tidak Aktif</option><option <?php echo $student['status']=='Lulus'?'selected':'';?>>Lulus</option><option <?php echo $student['status']=='Drop Out'?'selected':'';?>>Drop Out</option></select><br>
                        <button type="submit">Update</button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                        <input type="hidden" name="delete_student" value="1">
                        <button type="submit" onclick="return confirm('Hapus?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>