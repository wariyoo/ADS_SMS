<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'teacher') {
    $student_id = $_POST['student_id'] ?? null;
    $marks = $_POST['marks'] ?? [];

    if (!$student_id) {
        header("Location: index.php?error=Missing student ID");
        exit();
    }

    try {
        $pdo->beginTransaction();

        foreach ($marks as $offering_id => $mark_value) {
            // Skip if mark is empty (teacher didn't enter anything)
            if ($mark_value === "" || $mark_value === null) {
                // If the teacher cleared the mark, we might want to delete the MARK row?
                // The SRS says "Existence with mark NOT NULL = graded"
                // So if it's cleared, we delete it to make it "NG"
                $stmt = $pdo->prepare("DELETE FROM MARK WHERE student_id = ? AND offering_id = ?");
                $stmt->execute([$student_id, $offering_id]);
                continue;
            }

            // 1. Verify STUDENT_SUBJECT exists (Assignment)
            $stmt = $pdo->prepare("SELECT ss.*, s.total_mark FROM STUDENT_SUBJECT ss 
                                  JOIN SUBJECT_OFFERING so ON ss.offering_id = so.offering_id 
                                  JOIN SUBJECT s ON so.subject_id = s.subject_id 
                                  WHERE ss.student_id = ? AND ss.offering_id = ?");
            $stmt->execute([$student_id, $offering_id]);
            $assignment = $stmt->fetch();

            if (!$assignment) continue; // Should not happen with current UI

            // 2. Validate max mark
            if ($mark_value > $assignment['total_mark']) {
                throw new Exception("Mark for one or more subjects exceeds the total allowed.");
            }

            // 3. Upsert into MARK
            $stmt = $pdo->prepare("INSERT INTO MARK (student_id, offering_id, mark) VALUES (?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE mark = VALUES(mark)");
            $stmt->execute([$student_id, $offering_id, $mark_value]);
        }

        $pdo->commit();
        header("Location: enter_marks.php?student_id=$student_id&success=Marks updated successfully.");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header("Location: enter_marks.php?student_id=$student_id&error=Error updating marks: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
