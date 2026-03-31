<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $type = $_POST['type'] ?? '';

    try {
        if ($type === 'year') {
            $label = $_POST['year_label'] ?? '';
            $stmt = $pdo->prepare("INSERT IGNORE INTO ACADEMIC_YEAR (year_label) VALUES (?)");
            $stmt->execute([$label]);
            header("Location: manage_academic.php?success=Academic year added.");
        } elseif ($type === 'semester') {
            $year_id = $_POST['year_id'] ?? '';
            $label = $_POST['semester_label'] ?? '';
            $stmt = $pdo->prepare("INSERT IGNORE INTO SEMESTER (year_id, semester_label) VALUES (?, ?)");
            $stmt->execute([$year_id, $label]);
            header("Location: manage_academic.php?success=Semester added.");
        } elseif ($type === 'subject') {
            $name = $_POST['subject_name'] ?? '';
            $stmt = $pdo->prepare("INSERT IGNORE INTO SUBJECT (subject_name) VALUES (?)");
            $stmt->execute([$name]);
            header("Location: manage_academic.php?success=Subject added.");
        } elseif ($type === 'offering') {
            $subject_id = $_POST['subject_id'] ?? '';
            $semester_id = $_POST['semester_id'] ?? '';
            $stmt = $pdo->prepare("INSERT IGNORE INTO SUBJECT_OFFERING (subject_id, semester_id) VALUES (?, ?)");
            $stmt->execute([$subject_id, $semester_id]);
            header("Location: manage_academic.php?success=Subject offering linked.");
        }
    } catch (PDOException $e) {
        header("Location: manage_academic.php?error=Error: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
}
exit();
?>
