<?php
require_once '../config/db.php';
require_once '../reports/generate.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$semester_id = $_GET['semester_id'] ?? null;
$grade = $_SESSION['assigned_class'];

if (!$semester_id) {
    header("Location: class_report_select.php");
    exit();
}

$report = generateReportData($pdo, $semester_id, $grade);
$subjects = $report['subjects'];
$rows = $report['rows'];

// Fetch Teacher's Name for the report header
$teacher_stmt = $pdo->prepare("SELECT name FROM TEACHER WHERE teacher_id = ?");
$teacher_stmt->execute([$_SESSION['user_id']]);
$teacher_name = $teacher_stmt->fetchColumn();

// Fetch Year and Semester labels
$sem_stmt = $pdo->prepare("SELECT s.*, y.year_label FROM SEMESTER s JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id WHERE s.semester_id = ?");
$sem_stmt->execute([$semester_id]);
$sem_info = $sem_stmt->fetch();
?>

<main class="max-w-full mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center no-print">
        <a href="class_report_select.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-semibold transition-all flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Select
        </a>
        <button onclick="window.print()" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
            Print Roster
        </button>
    </div>

    <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-100 print-mode">
        <!-- Roster Header -->
        <div class="text-center mb-10 border-b-2 border-slate-900 pb-8">
            <h1 class="text-3xl font-black text-slate-900 uppercase">ABC HIGH SCHOOL STUDENT ROSTER</h1>
            <div class="grid grid-cols-2 mt-6 text-sm font-bold text-slate-700 uppercase tracking-widest gap-4">
                <div class="text-left">GRADE: <?php echo htmlspecialchars($grade); ?></div>
                <div class="text-right">HOMEROOM TEACHER: <?php echo htmlspecialchars($teacher_name); ?></div>
                <div class="text-left">ACADEMIC YEAR: <?php echo htmlspecialchars($sem_info['year_label'] ?? 'N/A'); ?></div>
                <div class="text-right">SEMESTER: <?php echo htmlspecialchars($sem_info['semester_label'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Roster Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-[10px] uppercase font-black">
                        <th class="px-3 py-4 border border-slate-700">NAME</th>
                        <th class="px-2 py-4 border border-slate-700 text-center">GEN</th>
                        <th class="px-3 py-4 border border-slate-700">ID</th>
                        <?php foreach ($subjects as $subj): ?>
                            <th class="px-2 py-4 border border-slate-700 text-center"><?php echo strtoupper(substr($subj['subject_name'], 0, 4)); ?></th>
                        <?php endforeach; ?>
                        <th class="px-2 py-4 border border-slate-700 text-center">TOTAL</th>
                        <th class="px-2 py-4 border border-slate-700 text-center">AVG</th>
                        <th class="px-2 py-4 border border-slate-700 text-center">RANK</th>
                        <th class="px-3 py-4 border border-slate-700 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody class="text-[11px] font-bold text-slate-800">
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?php echo count($subjects) + 7; ?>" class="px-6 py-10 text-center text-slate-400 italic">No student records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 py-3 border border-slate-200"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="px-2 py-3 border border-slate-200 text-center"><?php echo $row['gender']; ?></td>
                                <td class="px-3 py-3 border border-slate-200 font-mono"><?php echo htmlspecialchars($row['id_formatted']); ?></td>
                                <?php foreach ($subjects as $subj): ?>
                                    <td class="px-2 py-3 border border-slate-200 text-center">
                                        <?php 
                                        $m = $row['marks'][$subj['subject_name']]; 
                                        if ($m === 'NG') echo '<span class="text-amber-600">NG</span>';
                                        elseif ($m === 'N/A') echo '<span class="text-slate-300">N/A</span>';
                                        else echo $m;
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="px-2 py-3 border border-slate-200 text-center bg-slate-50"><?php echo $row['total']; ?></td>
                                <td class="px-2 py-3 border border-slate-200 text-center bg-slate-50"><?php echo $row['average']; ?></td>
                                <td class="px-2 py-3 border border-slate-200 text-center bg-indigo-50 text-indigo-700 font-black"><?php echo $row['rank']; ?></td>
                                <td class="px-3 py-3 border border-slate-200 text-center font-black <?php echo $row['status'] === 'PASS' ? 'text-green-600' : ($row['status'] === 'FAIL' ? 'text-red-600' : 'text-amber-500'); ?>">
                                    <?php echo $row['status']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-12 text-[10px] text-slate-400 font-bold italic">
            * NG: Not Graded | N/A: Not Applicable | This report is generated automatically by ADB-SMS.
        </div>
    </div>
</main>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
    .print-mode { border: none !important; box-shadow: none !important; }
    table { font-size: 9px; }
    th, td { padding: 4px !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
