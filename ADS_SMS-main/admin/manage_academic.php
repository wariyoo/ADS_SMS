<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all Data
$years = $pdo->query("SELECT * FROM ACADEMIC_YEAR ORDER BY year_label DESC")->fetchAll();
$subjects = $pdo->query("SELECT * FROM SUBJECT ORDER BY subject_name ASC")->fetchAll();
$semesters = $pdo->query("SELECT s.*, y.year_label FROM SEMESTER s JOIN ACADEMIC_YEAR y ON s.year_id = y.year_id ORDER BY y.year_label DESC, s.semester_label ASC")->fetchAll();
$offerings = $pdo->query("SELECT o.*, s.subject_name, sem.semester_label, y.year_label 
                         FROM SUBJECT_OFFERING o 
                         JOIN SUBJECT s ON o.subject_id = s.subject_id 
                         JOIN SEMESTER sem ON o.semester_id = sem.semester_id
                         JOIN ACADEMIC_YEAR y ON sem.year_id = y.year_id
                         ORDER BY y.year_label DESC, sem.semester_label ASC, s.subject_name ASC")->fetchAll();

$success = $_GET['success'] ?? null;
?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Academic Management</h1>
        <p class="text-slate-600">Configure years, semesters, and subject offerings.</p>
    </div>

    <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-green-700 shadow-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Academic Years & Semesters -->
        <div class="space-y-10">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Manage Academic Years</h2>
                <form action="academic_process.php" method="POST" class="flex space-x-3 mb-6">
                    <input type="hidden" name="type" value="year">
                    <input type="text" name="year_label" required class="flex-grow px-4 py-2 rounded-lg border border-slate-300 outline-none" placeholder="e.g., 2026">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">Add Year</button>
                </form>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($years as $y): ?>
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm font-semibold border border-slate-200">
                            <?php echo htmlspecialchars($y['year_label']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Add Semester</h2>
                <form action="academic_process.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="type" value="semester">
                    <div class="md:col-span-1">
                        <select name="year_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none bg-slate-50">
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y['year_id']; ?>"><?php echo htmlspecialchars($y['year_label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <input type="text" name="semester_label" required class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none" placeholder="e.g., Semester 1">
                    </div>
                    <button type="submit" class="md:col-span-1 bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">Add</button>
                </form>
            </div>
        </div>

        <!-- Subjects & Offerings -->
        <div class="space-y-10">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Manage Subjects</h2>
                <form action="academic_process.php" method="POST" class="flex space-x-3 mb-6">
                    <input type="hidden" name="type" value="subject">
                    <input type="text" name="subject_name" required class="flex-grow px-4 py-2 rounded-lg border border-slate-300 outline-none" placeholder="Subject Name">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">Add</button>
                </form>
                <div class="h-40 overflow-y-auto pr-2 border-t border-slate-100 pt-4">
                    <?php foreach ($subjects as $s): ?>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                            <span class="text-slate-700 font-medium"><?php echo htmlspecialchars($s['subject_name']); ?></span>
                            <span class="text-xs text-slate-400">Total: <?php echo $s['total_mark']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Create Subject Offering</h2>
                <form action="academic_process.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="type" value="offering">
                    <select name="subject_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none bg-slate-50">
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?php echo $s['subject_id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="semester_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 outline-none bg-slate-50">
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem['semester_id']; ?>"><?php echo htmlspecialchars($sem['year_label'] . " - " . $sem['semester_label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Link</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Offerings List -->
    <div class="mt-10 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 font-bold text-slate-700">Current Subject Offerings</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Academic Year</th>
                        <th class="px-6 py-4">Semester</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($offerings as $o): ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-bold text-slate-900"><?php echo htmlspecialchars($o['subject_name']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($o['year_label']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($o['semester_label']); ?></td>
                            <td class="px-6 py-4 text-right"><button class="text-red-400 hover:text-red-600 font-bold">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
