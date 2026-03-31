<?php
require_once '../config/db.php';
require_once '../reports/generate.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$semester_id = $_GET['semester_id'] ?? null;

if (!$semester_id) {
    header("Location: index.php?error=Select a semester first.");
    exit();
}

$report = generateReportData($pdo, $semester_id, null, $student_id);
$subjects = $report['subjects'];
$data = $report['rows'][0] ?? null; // Only one student
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition-colors font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                My Dashboard
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Academic Progress Report</h1>
            <p class="text-slate-600">Generated for <?php echo htmlspecialchars($data['academic_period'] ?? 'N/A'); ?></p>
        </div>
        <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow-md hover:shadow-lg transition-all no-print">
            Print Report
        </button>
    </div>

    <?php if (!$data): ?>
        <div class="bg-white p-12 rounded-2xl shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 italic text-lg">No academic records found for this period.</p>
        </div>
    <?php else: ?>
        <!-- School Header -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border-2 border-slate-100 mb-10 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 pb-6 border-b border-slate-100">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">ABC HIGH SCHOOL</h2>
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest letter-spacing-1 mt-1">Student Academic Record Management System</p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-400 font-bold uppercase mb-1">Generated On</div>
                    <div class="text-sm font-bold text-slate-700"><?php echo date('F d, Y'); ?></div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold mb-1">Name</span>
                    <span class="text-base font-bold text-slate-900"><?php echo htmlspecialchars($data['name']); ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold mb-1">ID Number</span>
                    <span class="text-base font-mono font-bold text-indigo-600"><?php echo htmlspecialchars($data['id_formatted']); ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold mb-1">Gender</span>
                    <span class="text-base font-bold text-slate-900"><?php echo $data['gender'] === 'M' ? 'Male' : 'Female'; ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold mb-1">Status</span>
                    <span class="px-3 py-1 rounded-full text-xs font-black <?php echo $data['status'] === 'PASS' ? 'bg-green-100 text-green-700' : ($data['status'] === 'FAIL' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>">
                        <?php echo $data['status']; ?>
                    </span>
                </div>
            </div>

            <!-- Marks Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Subject Name</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-center">Max Mark</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Obtained Mark</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($subjects as $subj): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-4 text-slate-700 font-bold"><?php echo htmlspecialchars($subj['subject_name']); ?></td>
                                <td class="px-6 py-4 text-center text-slate-400 font-mono text-sm">100</td>
                                <td class="px-6 py-4 text-right">
                                    <?php 
                                    $m = $data['marks'][$subj['subject_name']]; 
                                    if ($m === 'NG') echo '<span class="text-amber-500 font-bold italic">Not Graded</span>';
                                    elseif ($m === 'N/A') echo '<span class="text-slate-300 italic font-medium">N/A</span>';
                                    else echo '<span class="font-bold text-slate-900 text-lg">' . $m . '</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-slate-50/80 font-black">
                        <tr>
                            <td class="px-6 py-4 text-slate-500">SUMMARY</td>
                            <td class="px-6 py-4 text-center text-slate-400">TOTAL / AVG</td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-slate-900 text-base"><?php echo $data['total']; ?> / 500</div>
                                <div class="text-indigo-600 text-2xl"><?php echo $data['average']; ?>%</div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Verification Footer -->
            <div class="mt-12 flex justify-between items-end border-t-2 border-dashed border-slate-100 pt-8 no-print">
                <div class="flex items-center space-x-2 text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest italic">Authentic Academic Record</span>
                </div>
                <div class="text-center w-48 border-t border-slate-900 pt-2 no-print">
                    <span class="text-[10px] font-bold text-slate-900 uppercase">Principal Signature</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; padding: 0; margin: 0; }
    main { max-width: 100%; padding: 0; }
    .shadow-sm, .shadow-lg { border: none !important; box-shadow: none !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
