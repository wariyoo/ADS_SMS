<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $student_id = $_POST['student_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$student_id || !$action) {
        header("Location: index.php?error=Missing information");
        exit();
    }

    try {
        if ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE STUDENT SET status = 'Rejected' WHERE student_id = ?");
            $stmt->execute([$student_id]);
            header("Location: index.php?success=Registration rejected");
            exit();
        }

        if ($action === 'approve') {
            $pdo->beginTransaction();

            // 1. Fetch student info and requested period
            $stmt = $pdo->prepare("SELECT * FROM STUDENT WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();

            if (!$student) throw new Exception("Student not found");

            // 2. Update status and Generate formatted ID
            // Format: ABC[Count+1]/[YearShort]
            // We'll use a simple count or the primary key
            $year_lbl = "26"; // Default if not found
            if ($student['requested_year_id']) {
                $y_stmt = $pdo->prepare("SELECT year_label FROM ACADEMIC_YEAR WHERE year_id = ?");
                $y_stmt->execute([$student['requested_year_id']]);
                $year_lbl = substr($y_stmt->fetchColumn(), -2);
            }
            $formatted_id = "ABC" . str_pad($student_id, 3, '0', STR_PAD_LEFT) . "/" . $year_lbl;

            $stmt = $pdo->prepare("UPDATE STUDENT SET status = 'Active', student_id_formatted = ? WHERE student_id = ?");
            $stmt->execute([$formatted_id, $student_id]);

            // 3. Create ENROLLMENT
            if ($student['requested_year_id']) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO ENROLLMENT (student_id, year_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $student['requested_year_id']]);
            }

            // 4. Assign subjects (STUDENT_SUBJECT)
            if ($student['requested_semester_id']) {
                // Get all offerings for that semester
                $stmt = $pdo->prepare("SELECT offering_id FROM SUBJECT_OFFERING WHERE semester_id = ?");
                $stmt->execute([$student['requested_semester_id']]);
                $offerings = $stmt->fetchAll();

                foreach ($offerings as $offering) {
                    $assign_stmt = $pdo->prepare("INSERT IGNORE INTO STUDENT_SUBJECT (student_id, offering_id) VALUES (?, ?)");
                    $assign_stmt->execute([$student_id, $offering['offering_id']]);
                }
            }

            $pdo->commit();
            header("Location: index.php?success=Student approved and enrolled successfully! ID: $formatted_id");
            exit();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header("Location: index.php?error=Error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
