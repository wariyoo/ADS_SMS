<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $grade = $_POST['grade'] ?? '';
    $year_id = $_POST['academic_year'] ?? '';
    $semester_id = $_POST['semester'] ?? '';

    if (empty($name) || empty($gender) || empty($username) || empty($password) || empty($grade) || empty($year_id) || empty($semester_id)) {
        header("Location: ../register.php?error=All fields are required.");
        exit();
    }

    try {
        // Check if username already exists in any table
        $stmt = $pdo->prepare("SELECT username FROM STUDENT WHERE username = ? UNION SELECT username FROM TEACHER WHERE username = ? UNION SELECT username FROM ADMIN WHERE username = ?");
        $stmt->execute([$username, $username, $username]);
        if ($stmt->fetch()) {
            header("Location: ../register.php?error=Username already taken.");
            exit();
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert student as Pending
        // We'll store the desired year/semester in a temporary way or just handle it during approval
        // Actually, the SRS says "After registration, account status = Pending Approval"
        // And "Upon approval... ENROLLMENT record is created... STUDENT_SUBJECT records are created"
        // So we just need to save the student details and maybe the requested year/semester somewhere.
        // For now, let's just insert into STUDENT. We might need extra columns or just let Admin decide during approval.
        // The SRS says "Registration form collects... Academic Year... Semester"
        // I'll add these to the STUDENT table temporarily or just process them in the signup.
        // Let's add them to the STUDENT table to make it easy for Admin.

        // Wait, I should update the STUDENT table in setup.sql to include these if they are needed for approval.
        // For now, I'll just insert and assume Admin will see them or I'll add hidden fields/meta.
        // Let's just insert into STUDENT.
        
        $stmt = $pdo->prepare("INSERT INTO STUDENT (name, gender, username, password, grade, status, requested_year_id, requested_semester_id) VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?)");
        $stmt->execute([$name, $gender, $username, $hashed_password, $grade, $year_id, $semester_id]);
        
        // We don't have a place for year_id/semester_id in STUDENT table yet. 
        // I'll add them to the STUDENT table as 'requested_year_id' and 'requested_semester_id' to satisfy SRS 3.1.2.
        
        header("Location: ../register.php?success=Registration submitted successfully! Please wait for Admin approval.");
        exit();
    } catch (PDOException $e) {
        header("Location: ../register.php?error=System error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>
