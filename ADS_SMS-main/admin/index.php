<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Check if admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch pending registrations
try {
    $stmt = $pdo->query("SELECT s.*, y.year_label, sem.semester_label 
                        FROM STUDENT s 
                        LEFT JOIN ACADEMIC_YEAR y ON s.requested_year_id = y.year_id
                        LEFT JOIN SEMESTER sem ON s.requested_semester_id = sem.semester_id
                        WHERE s.status = 'Pending' 
                        ORDER BY s.created_at DESC");
    $pending_students = $stmt->fetchAll();

    // Fetch some stats
    $total_students = $pdo->query("SELECT COUNT(*) FROM STUDENT WHERE status = 'Active'")->fetchColumn();
    $total_teachers = $pdo->query("SELECT COUNT(*) FROM TEACHER")->fetchColumn();
    $total_subjects = $pdo->query("SELECT COUNT(*) FROM SUBJECT")->fetchColumn();
} catch (PDOException $e) {
    $pending_students = [];
    $total_students = $total_teachers = $total_subjects = 0;
}
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
        <p class="text-slate-600">Directly manage systemic academic operations.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-xl text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Active Students</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo $total_students; ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="bg-purple-100 p-3 rounded-xl text-purple-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Teachers</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo $total_teachers; ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="bg-emerald-100 p-3 rounded-xl text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Subjects Offered</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo $total_subjects; ?></p>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-10">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-900">Pending Approvals</h2>
            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold"><?php echo count($pending_students); ?> Requests</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-500 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Grade</th>
                        <th class="px-6 py-4 font-semibold">Requested Period</th>
                        <th class="px-6 py-4 font-semibold">Registration Date</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($pending_students)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">No pending registrations found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_students as $student): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <div class="text-xs text-slate-400">@<?php echo htmlspecialchars($student['username']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-600"><?php echo htmlspecialchars($student['grade']); ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-sm"><?php echo htmlspecialchars($student['year_label'] ?? 'N/A'); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($student['semester_label'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                <td class="px-6 py-4 text-right">
                                    <form action="approve_process.php" method="POST" class="inline-block">
                                        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                        <button name="action" value="approve" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-all shadow-sm hover:shadow-md">Approve</button>
                                        <button name="action" value="reject" class="text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 rounded-lg text-sm transition-all ml-2">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
