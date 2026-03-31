<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all students
try {
    $stmt = $pdo->query("SELECT s.*, y.year_label, sem.semester_label 
                        FROM STUDENT s 
                        LEFT JOIN ACADEMIC_YEAR y ON s.requested_year_id = y.year_id
                        LEFT JOIN SEMESTER sem ON s.requested_semester_id = sem.semester_id
                        ORDER BY s.status ASC, s.name ASC");
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
}

$success = $_GET['success'] ?? null;
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Student Management</h1>
            <p class="text-slate-600">View and manage all student accounts and enrollment status.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Student Name</th>
                        <th class="px-6 py-4 font-semibold">Formatted ID / Username</th>
                        <th class="px-6 py-4 font-semibold">Grade</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic font-medium">No students found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900"><?php echo htmlspecialchars($s['name']); ?></div>
                                    <div class="text-xs text-slate-400 font-medium capitalize"><?php echo $s['gender'] === 'M' ? 'Male' : 'Female'; ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono font-bold text-indigo-600"><?php echo htmlspecialchars($s['student_id_formatted'] ?: 'Not Assigned'); ?></div>
                                    <div class="text-xs text-slate-400">@<?php echo htmlspecialchars($s['username']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-600"><?php echo htmlspecialchars($s['grade']); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?php 
                                        echo $s['status'] === 'Active' ? 'bg-green-100 text-green-700' : 
                                            ($s['status'] === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'); 
                                    ?>">
                                        <?php echo $s['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($s['status'] === 'Pending'): ?>
                                        <a href="index.php" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">Review Request</a>
                                    <?php else: ?>
                                        <button class="text-slate-300 hover:text-red-500 transition-colors ml-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    <?php endif; ?>
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
