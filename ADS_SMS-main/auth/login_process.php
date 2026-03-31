<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password) || empty($role)) {
        header("Location: ../index.php?error=Please fill all fields");
        exit();
    }

    try {
        $user = null;
        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM ADMIN WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
        } elseif ($role === 'teacher') {
            $stmt = $pdo->prepare("SELECT * FROM TEACHER WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
        } elseif ($role === 'student') {
            $stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['status'] === 'Pending') {
                header("Location: ../index.php?error=Your account is pending approval by Admin.");
                exit();
            } elseif ($user && $user['status'] === 'Rejected') {
                header("Location: ../index.php?error=Your account registration was rejected.");
                exit();
            }
        }

        if ($user && password_verify($password, $user['password'])) {
            // Password correct, start session
            $_SESSION['user_id'] = ($role === 'admin') ? $user['admin_id'] : 
                                   (($role === 'teacher') ? $user['teacher_id'] : $user['student_id']);
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'] ?? $user['username'];
            $_SESSION['role'] = $role;
            
            if ($role === 'teacher') {
                $_SESSION['assigned_class'] = $user['assigned_class'];
            }

            header("Location: ../$role/index.php");
            exit();
        } else {
            header("Location: ../index.php?error=Invalid username or password");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../index.php?error=System error. Please try again later.");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
