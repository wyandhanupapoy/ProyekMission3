<?php
// courses.php - Kelola Courses (Admin: CRUD, Student: View & Enroll)
session_start();
require_once 'config.php';

$role = requireLogin($pdo);

if ($role === 'Admin') {
    // CRUD untuk Admin
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_course'])) {
            $course_code = $_POST['course_code'];
            $course_name = $_POST['course_name'];
            $credits = $_POST['credits'];
            $semester = $_POST['semester'];
            $description = $_POST['description'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, credits, semester, description, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$course_code, $course_name, $credits, $semester, $description, $status]);
            $message = 'Course ditambahkan!';
        } elseif (isset($_POST['edit_course'])) {
            $course_id = $_POST['course_id'];
            $course_code = $_POST['course_code'];
            $course_name = $_POST['course_name'];
            $credits = $_POST['credits'];
            $semester = $_POST['semester'];
            $description = $_POST['description'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("UPDATE courses SET course_code=?, course_name=?, credits=?, semester=?, description=?, status=? WHERE course_id=?");
            $stmt->execute([$course_code, $course_name, $credits, $semester, $description, $status, $course_id]);
            $message = 'Course diupdate!';
        } elseif (isset($_POST['delete_course'])) {
            $course_id = $_POST['course_id'];
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id=?");
            $stmt->execute([$course_id]);
            $message = 'Course dihapus!';
        }
    }

    // Ambil daftar courses
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY course_code");
    $courses = $stmt->fetchAll();
} else {
    // Untuk Student: View courses dan enroll
    $student_id = $_SESSION['user_id']; // Asumsi student_id = user_id

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
        $course_id = $_POST['course_id'];
        $enroll_date = date('Y-m-d');

        // Cek apakah sudah enrolled
        $check = $pdo->prepare("SELECT id FROM takes WHERE student_id=? AND course_id=?");
        $check->execute([$student_id, $course_id]);
        if (!$check->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO takes (student_id, course_id, enroll_date, status) VALUES (?, ?, ?, 'Enrolled')");
            $stmt->execute([$student_id, $course_id, $enroll_date]);
            $message = 'Berhasil enroll!';
        } else {
            $message = 'Sudah enrolled!';
        }
    }

    // Ambil courses yang tersedia (Aktif)
    $stmt = $pdo->query("SELECT * FROM courses WHERE status='Aktif' ORDER BY course_code");
    $courses = $stmt->fetchAll();

    // Ambil enrolled courses untuk student
    $enrolled_stmt = $pdo->prepare("SELECT c.* FROM courses c JOIN takes t ON c.course_id = t.course_id WHERE t.student_id=? AND t.status='Enrolled'");
    $enrolled_stmt->execute([$student_id]);
    $enrolled = $enrolled_stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $role === 'Admin' ? 'Kelola' : 'Daftar'; ?> Courses</title>
    <style>body { font-family: Arial; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; }</style>
</head>
<body>
    <?php echo getNavigation($role); ?>
    <h1><?php echo $role === 'Admin' ? 'Kelola Courses' : 'Daftar Courses'; ?></h1>
    <?php if (isset($message)): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>

    <?php if ($role === 'Admin'): ?>
        <!-- Form Tambah Course -->
        <h2>Tambah Course</h2>
        <form method="POST">
            <input type="hidden" name="add_course" value="1">
            Code: <input type="text" name="course_code" required><br>
            Name: <input type="text" name="course_name" required><br>
            Credits: <input type="number" name="credits" required><br>
            Semester: <input type="number" name="semester"><br>
            Description: <textarea name="description"></textarea><br>
            Status: <select name="status"><option>Aktif</option><option>Tidak Aktif</option></select><br>
            <button type="submit">Tambah</button>
        </form>

        <!-- Daftar Courses dengan Edit/Delete -->
        <table>
            <tr><th>ID</th><th>Code</th><th>Name</th><th>Credits</th><th>Semester</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo $course['course_id']; ?></td>
                    <td><?php echo $course['course_code']; ?></td>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['credits']; ?></td>
                    <td><?php echo $course['semester']; ?></td>
                    <td><?php echo $course['status']; ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <input type="hidden" name="edit_course" value="1">
                            <!-- Sederhana: Edit via form terpisah, tapi untuk simplicity, kita buat inline partial -->
                            Code: <input type="text" name="course_code" value="<?php echo $course['course_code']; ?>"><br>
                            <!-- Ini sederhana, bisa expand ke modal, tapi untuk demo cukup -->
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <input type="hidden" name="delete_course" value="1">
                            <button type="submit" onclick="return confirm('Hapus?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <!-- Untuk Student: Daftar Courses dengan Enroll -->
        <h2>Courses Tersedia</h2>
        <table>
            <tr><th>Code</th><th>Name</th><th>Credits</th><th>Semester</th><th>Action</th></tr>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo $course['course_code']; ?></td>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['credits']; ?></td>
                    <td><?php echo $course['semester']; ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <input type="hidden" name="enroll" value="1">
                            <button type="submit">Enroll</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Courses yang Di-enroll</h2>
        <table>
            <tr><th>Code</th><th>Name</th><th>Credits</th></tr>
            <?php foreach ($enrolled as $course): ?>
                <tr>
                    <td><?php echo $course['course_code']; ?></td>
                    <td><?php echo $course['course_name']; ?></td>
                    <td><?php echo $course['credits']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>