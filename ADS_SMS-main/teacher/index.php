<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$assigned_class = $_SESSION['assigned_class'];

// Fetch students in the assigned class who are enrolled in the current academic year/semester
// For simplicity, we'll fetch all active students in the assigned class
try {
    $stmt = $pdo->prepare("SELECT s.*, e.enrollment_date 
                          FROM STUDENT s 
                          JOIN ENROLLMENT e ON s.student_id = e.student_id
                          WHERE s.grade = ? AND s.status = 'Active'
                          ORDER BY s.name ASC");
    $stmt->execute([$assigned_class]);
    $students = $stmt->fetchAll();

    // Fetch subjects currently offered
    $subjects_stmt = $pdo->query("SELECT so.*, s.subject_name, y.year_label, sem.semester_label 
                                 FROM SUBJECT_OFFERING so 
                                 JOIN SUBJECT s ON so.subject_id = s.subject_id
                                 JOIN SEMESTER sem ON so.semester_id = sem.semester_id
                                 JOIN ACADEMIC_YEAR y ON sem.year_id = y.year_id
                                 ORDER BY y.year_label DESC, sem.semester_label ASC");
    $offerings = $subjects_stmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
    $offerings = [];
}
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Class Roster - <?php echo htmlspecialchars($assigned_class); ?></h1>
            <p class="text-slate-600">Enter and update marks for your homeroom students.</p>
        </div>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100">
            <span class="text-sm text-indigo-700 font-bold">Total Students: <?php echo count($students); ?></span>
        </div>
    </div>

    <!-- Mark Entry Logic Help -->
    <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-4 rounded text-blue-700 text-sm italic">
        <strong>Row Existence Principle:</strong> Marks can only be entered for subjects where the student is assigned (STUDENT_SUBJECT row exists). 
        Absence of a mark row means "Assigned but not graded (NG)".
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Student Name</th>
                        <th class="px-6 py-4 font-semibold">Formatted ID</th>
                        <th class="px-6 py-4 font-semibold">Gender</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">No active students in your assigned class.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <div class="text-xs text-slate-400">@<?php echo htmlspecialchars($student['username']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-indigo-600 font-bold"><?php echo htmlspecialchars($student['student_id_formatted']); ?></td>
                                <td class="px-6 py-4 text-sm"><?php echo $student['gender'] === 'M' ? 'Male' : 'Female'; ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="enter_marks.php?student_id=<?php echo $student['student_id']; ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm hover:shadow-md inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Manage Marks
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Class Report Link -->
    <div class="mt-10 flex justify-center">
        <a href="class_report_select.php" class="bg-slate-800 hover:bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold flex items-center transition-all shadow-lg hover:shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Generate Class Roster Report
        </a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
