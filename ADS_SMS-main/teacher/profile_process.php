<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        header("Location: profile.php?error=No changes made.");
        exit();
    }

    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE TEACHER SET password = ? WHERE teacher_id = ?");
        $stmt->execute([$hashed_password, $teacher_id]);

        header("Location: profile.php?success=Password updated successfully!");
        exit();
    } catch (PDOException $e) {
        header("Location: profile.php?error=Error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
