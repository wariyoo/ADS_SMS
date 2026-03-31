<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    $student_id = $_SESSION['user_id'];
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($gender)) {
        header("Location: index.php?error=Name and Gender are required.");
        exit();
    }

    try {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE STUDENT SET name = ?, gender = ?, password = ? WHERE student_id = ?");
            $stmt->execute([$name, $gender, $hashed_password, $student_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE STUDENT SET name = ?, gender = ? WHERE student_id = ?");
            $stmt->execute([$name, $gender, $student_id]);
        }

        // Update session name
        $_SESSION['name'] = $name;

        header("Location: index.php?success=Profile updated successfully!");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
