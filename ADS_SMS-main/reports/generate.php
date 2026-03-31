<?php
/**
 * Core Report Generation Logic for ADB SMS
 */

require_once __DIR__ . '/../config/db.php';

function generateReportData($pdo, $semester_id, $grade = null, $student_id = null) {
    // 1. Fetch all subjects offered in this semester
    $stmt = $pdo->prepare("SELECT so.offering_id, s.subject_name 
                          FROM SUBJECT_OFFERING so 
                          JOIN SUBJECT s ON so.subject_id = s.subject_id 
                          WHERE so.semester_id = ? 
                          ORDER BY s.subject_name ASC");
    $stmt->execute([$semester_id]);
    $offered_subjects = $stmt->fetchAll();

    // 2. Fetch students based on grade or specific student_id
    $query = "SELECT s.*, y.year_label, sem.semester_label 
              FROM STUDENT s 
              JOIN SEMESTER sem ON sem.semester_id = ?
              JOIN ACADEMIC_YEAR y ON sem.year_id = y.year_id
              WHERE s.status = 'Active'";
    $params = [$semester_id];

    if ($student_id) {
        $query .= " AND s.student_id = ?";
        $params[] = $student_id;
    } elseif ($grade) {
        $query .= " AND s.grade = ?";
        $params[] = $grade;
    }
    
    $query .= " ORDER BY s.name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    $report_data = [];

    // 3. For each student, fetch marks and calculate stats
    foreach ($students as $student) {
        $student_marks = [];
        $total = 0;
        $graded_count = 0;
        $has_fail = false;
        $all_graded = true;

        foreach ($offered_subjects as $subj) {
            // Check if assigned (STUDENT_SUBJECT)
            $as_stmt = $pdo->prepare("SELECT 1 FROM STUDENT_SUBJECT WHERE student_id = ? AND offering_id = ?");
            $as_stmt->execute([$student['student_id'], $subj['offering_id']]);
            $is_assigned = $as_stmt->fetch();

            if (!$is_assigned) {
                $student_marks[$subj['subject_name']] = 'N/A';
                continue;
            }

            // Check if graded (MARK)
            $m_stmt = $pdo->prepare("SELECT mark FROM MARK WHERE student_id = ? AND offering_id = ?");
            $m_stmt->execute([$student['student_id'], $subj['offering_id']]);
            $mark_row = $m_stmt->fetch();

            if ($mark_row) {
                $mark = $mark_row['mark'];
                $student_marks[$subj['subject_name']] = $mark;
                $total += $mark;
                $graded_count++;
                if ($mark < 50) $has_fail = true;
            } else {
                $student_marks[$subj['subject_name']] = 'NG'; // Not Graded
                $all_graded = false;
            }
        }

        // Calculations
        $average = $graded_count > 0 ? round($total / $graded_count, 2) : 0;
        
        $status = 'Pending';
        if ($graded_count > 0) {
            $status = $has_fail ? 'FAIL' : 'PASS';
        }

        $report_data[] = [
            'student_id' => $student['student_id'],
            'name' => $student['name'],
            'gender' => $student['gender'],
            'id_formatted' => $student['student_id_formatted'],
            'marks' => $student_marks,
            'total' => $total,
            'average' => $average,
            'status' => $status,
            'graded_count' => $graded_count,
            'academic_period' => $student['year_label'] . " | " . $student['semester_label']
        ];
    }

    // 4. Calculate Rank
    // Rank is based on total marks descending within the same grade/semester
    usort($report_data, function($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    foreach ($report_data as $index => &$row) {
        $row['rank'] = ($row['graded_count'] > 0) ? ($index + 1) : '-';
    }

    // Sort back by name for the final display if needed, but usually roster is by rank or name.
    // We'll keep it by rank for now.

    return [
        'subjects' => $offered_subjects,
        'rows' => $report_data
    ];
}
?>
