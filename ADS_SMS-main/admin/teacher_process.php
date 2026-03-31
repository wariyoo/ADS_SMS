<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $name = $_POST['name'] ?? '';
    $department_id = $_POST['department_id'] ?? '';
    $assigned_class = $_POST['assigned_class'] ?? '';

    if (empty($name) || empty($department_id)) {
        header("Location: manage_teachers.php?error=Name and Department are required.");
        exit();
    }

    try {
        // Generate Username: teacher.firstname.lastname
        $parts = explode(' ', strtolower(trim($name)));
        $firstname = $parts[0] ?? 'teacher';
        $lastname = $parts[1] ?? 'user';
        $base_username = "teacher." . $firstname . "." . $lastname;
        
        // Ensure uniqueness
        $username = $base_username;
        $counter = 1;
        while (true) {
            $stmt = $pdo->prepare("SELECT username FROM TEACHER WHERE username = ? UNION SELECT username FROM ADMIN WHERE username = ? UNION SELECT username FROM STUDENT WHERE username = ?");
            $stmt->execute([$username, $username, $username]);
            if (!$stmt->fetch()) break;
            $username = $base_username . $counter++;
        }

        // Generate Random Password (8 chars)
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $password = substr(str_shuffle($chars), 0, 8);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $stmt = $pdo->prepare("INSERT INTO TEACHER (name, username, password, department_id, assigned_class) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $username, $hashed_password, $department_id, $assigned_class]);

        // Save credentials to session to show once
        $_SESSION['new_teacher_creds'] = [
            'username' => $username,
            'password' => $password
        ];

        header("Location: manage_teachers.php?success=Teacher added successfully!");
        exit();
    } catch (PDOException $e) {
        header("Location: manage_teachers.php?error=Database error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
